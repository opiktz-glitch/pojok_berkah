<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';
require_once __DIR__ . '/../lib/normalize.php';

function handleSaveArtikel(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
{
    if (!$isAuthenticated) {
        return $settings;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return $settings;
    }
    $submittedCsrf = $_POST['csrf'] ?? '';
    if (!verifyCsrf((string) $submittedCsrf, $csrf)) {
        http_response_code(403);
        exit;
    }
    $action = $_POST['action'] ?? '';
    if ($action !== 'save_artikel') {
        return $settings;
    }

    $title = trim((string) ($_POST['artikel_title'] ?? ''));
    $slugInput = trim((string) ($_POST['artikel_slug'] ?? ''));
    $excerpt = trim((string) ($_POST['artikel_excerpt'] ?? ''));
    $content = trim((string) ($_POST['artikel_content'] ?? ''));
    $status = trim((string) ($_POST['artikel_status'] ?? 'draft'));
    $editingSlug = trim((string) ($_POST['artikel_editing_slug'] ?? ''));
    $imageAlt = trim((string) ($_POST['artikel_image_alt'] ?? ''));
    $removeImage = isset($_POST['artikel_remove_image']) && $_POST['artikel_remove_image'] === '1';

    if ($title === '' || mb_strlen($title) < 5 || mb_strlen($title) > 120) {
        $flash = 'Judul artikel 5-120 karakter.';
        return $settings;
    }
    if ($excerpt === '' || mb_strlen($excerpt) < 20 || mb_strlen($excerpt) > 320) {
        $flash = 'Ringkasan 20-320 karakter.';
        return $settings;
    }
    if ($content === '' || mb_strlen($content) < 100) {
        $flash = 'Isi artikel minimal 100 karakter.';
        return $settings;
    }
    if (mb_strlen($content) > 30000) {
        $flash = 'Isi artikel maksimal 30000 karakter.';
        return $settings;
    }
    if (!in_array($status, ['draft', 'published'], true)) {
        $status = 'draft';
    }

    $uploadedImage = null;
    if (isset($_FILES['artikel_image']) && is_array($_FILES['artikel_image'])
        && ($_FILES['artikel_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['artikel_image'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
            $flash = 'Gagal mengunggah gambar artikel.';
            return $settings;
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $flash = 'Ukuran gambar artikel maksimal 2 MB.';
            return $settings;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string)$file['tmp_name']);
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowedImageTypes, true) || @getimagesize((string)$file['tmp_name']) === false) {
            $flash = 'Format gambar artikel harus JPG, PNG, atau WebP yang valid.';
            return $settings;
        }
        if ($imageAlt === '') {
            $flash = 'Isi teks alternatif untuk gambar artikel.';
            return $settings;
        }
        $imageData = file_get_contents((string)$file['tmp_name']);
        if ($imageData === false) {
            $flash = 'Gambar artikel tidak bisa dibaca.';
            return $settings;
        }
        $uploadedImage = 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }

    // Slug: pakai input jika diisi, fallback dari judul
    $desiredSlug = $slugInput !== '' ? $slugInput : $title;
    $articles = isset($settings['articles']) && is_array($settings['articles']) ? $settings['articles'] : [];
    $existingSlugs = [];
    foreach ($articles as $a) {
        if (isset($a['slug']) && is_string($a['slug'])) {
            $existingSlugs[] = $a['slug'];
        }
    }

    // Saat edit, keluarkan slug lama dari daftar agar tidak dianggap duplikat
    $filteredSlugs = $existingSlugs;
    if ($editingSlug !== '') {
        $filteredSlugs = array_values(array_filter($existingSlugs, function ($s) use ($editingSlug) {
            return $s !== $editingSlug;
        }));
    }

    $slug = uniqueArticleSlug($desiredSlug, $filteredSlugs, $editingSlug !== '' ? $editingSlug : '');
    // Validasi final slug
    $normalized = normalizeArticleSlug($slug);
    if ($normalized === null) {
        $flash = 'Slug tidak valid. Gunakan huruf/angka/strip, 3-60 karakter.';
        return $settings;
    }
    $slug = $normalized;

    $now = date('Y-m-d');

    // Jika editingSlug ada, update. Jika tidak, create baru.
    $foundIndex = -1;
    if ($editingSlug !== '') {
        foreach ($articles as $idx => $a) {
            if (($a['slug'] ?? '') === $editingSlug) {
                $foundIndex = $idx;
                break;
            }
        }
    }

    $newArticle = [
        'slug' => $slug,
        'title' => $title,
        'excerpt' => $excerpt,
        'content' => $content,
        'status' => $status,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if ($uploadedImage !== null) {
        $newArticle['image'] = $uploadedImage;
        $newArticle['image_alt'] = $imageAlt;
    } elseif ($foundIndex >= 0 && !$removeImage) {
        if (!empty($articles[$foundIndex]['image'])) {
            $newArticle['image'] = $articles[$foundIndex]['image'];
            $newArticle['image_alt'] = $imageAlt !== '' ? $imageAlt : (string)($articles[$foundIndex]['image_alt'] ?? '');
        }
    }

    if ($foundIndex >= 0) {
        // Pertahankan created_at lama
        $newArticle['created_at'] = $articles[$foundIndex]['created_at'] ?? $now;
        $articles[$foundIndex] = $newArticle;
        $flash = 'Artikel "' . $title . '" diperbarui. Slug: ' . $slug . '.html';
    } else {
        // Cek duplikat slug sudah ditangani uniqueArticleSlug, tapi double-check
        $articles[] = $newArticle;
        $flash = 'Artikel "' . $title . '" ditambahkan. Slug: ' . $slug . '.html';
    }

    $settings['articles'] = array_values($articles);
    if (saveSettings($storagePath, $settings)) {
        // flash sudah diset
    } else {
        $flash = 'Gagal menyimpan artikel. Pastikan folder data bisa ditulis.';
    }

    return $settings;
}

function handleDeleteArtikel(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
{
    if (!$isAuthenticated) {
        return $settings;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return $settings;
    }
    $submittedCsrf = $_POST['csrf'] ?? '';
    if (!verifyCsrf((string) $submittedCsrf, $csrf)) {
        http_response_code(403);
        exit;
    }
    $action = $_POST['action'] ?? '';
    if ($action !== 'delete_artikel') {
        return $settings;
    }
    if (($_POST['confirm_delete'] ?? '') !== '1') {
        $flash = 'Centang "Yakin hapus" untuk menghapus artikel.';
        return $settings;
    }
    $slug = trim((string) ($_POST['artikel_slug'] ?? ''));
    if ($slug === '') {
        $flash = 'Slug artikel tidak ditemukan.';
        return $settings;
    }
    $articles = isset($settings['articles']) && is_array($settings['articles']) ? $settings['articles'] : [];
    $before = count($articles);
    $articles = array_values(array_filter($articles, function ($a) use ($slug) {
        return ($a['slug'] ?? '') !== $slug;
    }));
    if (count($articles) === $before) {
        $flash = 'Artikel tidak ditemukan.';
        return $settings;
    }
    $settings['articles'] = $articles;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Artikel dihapus.';
    } else {
        $flash = 'Gagal menghapus artikel.';
    }
    return $settings;
}
