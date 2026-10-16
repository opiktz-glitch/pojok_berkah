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
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes, true)) {
        $flash = 'Format gambar tidak didukung. Gunakan JPEG, PNG, atau WebP.';
        return $settings;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $flash = 'Ukuran gambar maksimal 5 MB.';
        return $settings;
    }

    $imageData = file_get_contents($file['tmp_name']);
    if ($imageData === false) {
        $flash = 'Gagal membaca file gambar.';
        return $settings;
    }

    $base64 = 'data:' . $file['type'] . ';base64,' . base64_encode($imageData);

    if (!isset($settings['images'])) {
        $settings['images'] = [];
    }
    $settings['images'][$imageSlot] = $base64;

    if (saveSettings($storagePath, $settings)) {
        $flash = 'Gambar berhasil diunggah.';
    } else {
        $flash = 'Gambar tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleRemoveImage(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
{
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

    if (saveSettings($storagePath, $settings)) {
        $flash = 'Gambar kembali ke bawaan.';
    } else {
        $flash = 'Gagal mengembalikan gambar bawaan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}