<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';

$defaultServiceNames = [
    'tryout' => 'Tryout TKA',
    'gadai' => 'Gadai BPKB',
    'kitchen' => 'Alfi Kitchen',
    'parkir' => 'Aplikasi Parkir',
];

$defaultServiceUrls = [
    'tryout' => 'https://tryout.pojokberkah.online',
    'gadai' => 'https://gadai.pojokberkah.online',
    'kitchen' => 'https://kitchen.pojokberkah.online',
    'parkir' => 'https://parkir.pojokberkah.online',
];

$defaultServiceDescriptions = [
    'tryout' => 'Latihan soal dan simulasi ujian TKA untuk persiapan seleksi cpns.',
    'gadai' => 'Informasi simulasi gadai BPKB dengan estimasi nilai jaminan.',
    'kitchen' => 'Puding berlapis buah, dessert dalam kemasan praktis, dan salad buah bersaus creamy.',
    'parkir' => 'Sistem manajemen parkir digital untuk lingkungan perkantoran dan perumahan.',
];

$defaultServiceTags = [
    'tryout' => ['TKA', 'CPNS', 'Seleksi'],
    'gadai' => ['BPKB', 'Gadai', 'Simulasi'],
    'kitchen' => ['Dessert', 'Buatan Rumahan', 'Alfi Kitchen'],
    'parkir' => ['Parkir', 'Digital', 'Sistem'],
];

function handleSaveServiceNames(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_service_names') {
        return $settings;
    }

    $names = [];
    foreach ($defaultServiceNames as $slot => $defaultName) {
        $name = trim($_POST['service_names'][$slot] ?? '');
        if ($name === '' || strlen($name) > 400) {
            $flash = 'Nama layanan wajib diisi dan maksimal 400 karakter.';
            return $settings;
        }
        $names[$slot] = $name;
    }

    $settings['service_names'] = $names;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Nama layanan berhasil diperbarui.';
    } else {
        $flash = 'Nama layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleSaveServiceDescriptions(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_service_descriptions') {
        return $settings;
    }

    $descriptions = [];
    foreach ($defaultServiceDescriptions as $slot => $defaultDescription) {
        $description = trim($_POST['service_descriptions'][$slot] ?? '');
        if ($description === '' || strlen($description) > 500) {
            $flash = 'Deskripsi wajib diisi dan maksimal 500 karakter.';
            return $settings;
        }
        $descriptions[$slot] = $description;
    }

    $settings['service_descriptions'] = $descriptions;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Deskripsi layanan berhasil diperbarui.';
    } else {
        $flash = 'Deskripsi layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}
function handleSaveServiceUrls(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_service_urls') {
        return $settings;
    }

    $urls = [];
    foreach ($defaultServiceUrls as $slot => $defaultUrl) {
        $url = trim($_POST['service_urls'][$slot] ?? '');
        if ($url === '') {
            $flash = 'Masukkan URL penuh yang valid dengan awalan http:// atau https:// untuk setiap layanan.';
            return $settings;
        }
        $normalized = normalizeServiceUrl($url);
        if ($normalized === null) {
            $flash = 'Masukkan URL penuh yang valid dengan awalan http:// atau https:// untuk setiap layanan.';
            return $settings;
        }
        $urls[$slot] = $normalized;
    }

    $settings['service_urls'] = $urls;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Alamat layanan berhasil diperbarui.';
    } else {
        $flash = 'Alamat layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleSaveServiceTags(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_service_tags') {
        return $settings;
    }

    $tags = [];
    foreach ($defaultServiceTags as $slot => $defaultTags) {
        $raw = trim($_POST['service_tags'][$slot] ?? '');
        $tagList = [];
        if ($raw !== '') {
            $lines = explode("\n", $raw);
            foreach ($lines as $line) {
                $tag = trim($line);
                if ($tag !== '' && count($tagList) < 8 && strlen($tag) <= 60) {
                    $tagList[] = $tag;
                }
            }
        }
        $tags[$slot] = $tagList;
    }

    $settings['service_tags'] = $tags;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Tag layanan berhasil diperbarui.';
    } else {
        $flash = 'Tag layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}