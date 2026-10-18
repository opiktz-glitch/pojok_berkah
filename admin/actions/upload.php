<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';

$imageSlots = [
    'logo' => 'Logo situs',
    'tryout' => 'Tryout TKA',
    'gadai' => 'Gadai BPKB',
    'kitchen' => 'Alfi Kitchen',
    'parkir' => 'Aplikasi Parkir',
];

function handleUploadImage(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
{
    global $imageSlots;
    if (!$isAuthenticated) {
        return $settings;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $settings;
    }

    $submittedCsrf = $_POST['csrf'] ?? '';
    if (!verifyCsrf($submittedCsrf, $csrf)) {
        http_response_code(403);
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action !== 'upload_image') {
        return $settings;
    }

    $imageSlot = $_POST['image_slot'] ?? '';
    if (!array_key_exists($imageSlot, $imageSlots)) {
        $flash = 'Slot gambar tidak valid.';
        return $settings;
    }

    if (!isset($_FILES['service_image']) || $_FILES['service_image']['error'] !== UPLOAD_ERR_OK) {
        $flash = 'Gagal mengunggah gambar.';
        return $settings;
    }

    $file = $_FILES['service_image'];
    if (!is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
        $flash = 'Gagal mengunggah gambar.';
        return $settings;
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        $flash = 'Ukuran gambar maksimal 5 MB.';
        return $settings;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string)$file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedTypes, true) || @getimagesize((string)$file['tmp_name']) === false) {
        $flash = 'Format gambar tidak didukung. Gunakan JPEG, PNG, atau WebP yang valid.';
        return $settings;
    }

    $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ext = $extMap[$mime] ?? 'jpg';
    $uploadDir = dirname($storagePath) . '/uploads';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        $flash = 'Folder upload tidak bisa dibuat.';
        return $settings;
    }
    foreach (['jpg', 'png', 'webp'] as $oldExt) {
        $oldFile = $uploadDir . '/service_' . $imageSlot . '.' . $oldExt;
        if (is_file($oldFile) && $oldExt !== $ext) {
            @unlink($oldFile);
        }
    }
    $destFile = $uploadDir . '/service_' . $imageSlot . '.' . $ext;
    $imageData = file_get_contents((string)$file['tmp_name']);
    if ($imageData === false) {
        $flash = 'Gagal membaca file gambar.';
        return $settings;
    }
    if (file_put_contents($destFile, $imageData, LOCK_EX) === false) {
        $flash = 'Gagal menyimpan file gambar.';
        return $settings;
    }

    if (!isset($settings['images'])) {
        $settings['images'] = [];
    }
    $settings['images'][$imageSlot] = 'data/uploads/service_' . $imageSlot . '.' . $ext . '?v=' . substr(sha1($imageData), 0, 8);

    if (saveSettings($storagePath, $settings)) {
        $flash = 'Gambar berhasil diunggah.';
    } else {
        $flash = 'Gambar tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleRemoveImage(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
{
    global $imageSlots;
    if (!$isAuthenticated) {
        return $settings;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return $settings;
    }

    $submittedCsrf = $_POST['csrf'] ?? '';
    if (!verifyCsrf($submittedCsrf, $csrf)) {
        http_response_code(403);
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action !== 'remove_image') {
        return $settings;
    }

    $imageSlot = $_POST['image_slot'] ?? '';
    if (!array_key_exists($imageSlot, $imageSlots)) {
        $flash = 'Slot gambar tidak valid.';
        return $settings;
    }

    if (!isset($settings['images'])) {
        $settings['images'] = [];
    }
    unset($settings['images'][$imageSlot]);
    $uploadDir = dirname($storagePath) . '/uploads';
    foreach (['jpg', 'png', 'webp'] as $ext) {
        $oldFile = $uploadDir . '/service_' . $imageSlot . '.' . $ext;
        if (is_file($oldFile)) {
            @unlink($oldFile);
        }
    }

    if (saveSettings($storagePath, $settings)) {
        $flash = 'Gambar kembali ke bawaan.';
    } else {
        $flash = 'Gagal mengembalikan gambar bawaan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}
