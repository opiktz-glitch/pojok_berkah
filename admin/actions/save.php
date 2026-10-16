<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';
require_once __DIR__ . '/../lib/normalize.php';

function handleSaveWhatsApp(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save') {
        return $settings;
    }

    $waNumber = normalizeWhatsAppNumber($_POST['wa_number'] ?? '');
    if ($waNumber === null) {
        $flash = 'Nomor WhatsApp tidak valid.';
        return $settings;
    }

    $settings['wa_number'] = $waNumber;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Nomor WhatsApp berhasil disimpan.';
    } else {
        $flash = 'Nomor WhatsApp tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleSaveStats(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_stats') {
        return $settings;
    }

    $stats = [
        'services' => [
            'value' => trim($_POST['stats']['services']['value'] ?? ''),
            'label' => trim($_POST['stats']['services']['label'] ?? ''),
        ],
        'gadai' => [
            'value' => trim($_POST['stats']['gadai']['value'] ?? ''),
            'label' => trim($_POST['stats']['gadai']['label'] ?? ''),
        ],
        'kitchen' => [
            'value' => trim($_POST['stats']['kitchen']['value'] ?? ''),
            'label' => trim($_POST['stats']['kitchen']['label'] ?? ''),
        ],
    ];

    $settings['stats'] = $stats;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Statistik berhasil disimpan.';
    } else {
        $flash = 'Statistik tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}