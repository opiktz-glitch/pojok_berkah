<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';

function handleSetup(array $settings, string $storagePath, string $csrf, bool $setupRequired, bool $canSetup, string &$flash): array
{
    if (!$setupRequired || !$canSetup) {
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

    if ($action !== 'setup') {
        return $settings;
    }

    $password = $_POST['password'] ?? '';
    $confirmation = $_POST['password_confirmation'] ?? '';

    if (!isValidAdminPassword($password)) {
        $flash = 'Password minimal 6 karakter dan mengandung huruf serta angka.';
        return $settings;
    }

    if ($password !== $confirmation) {
        $flash = 'Konfirmasi password tidak cocok.';
        return $settings;
    }

    $settings['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Akun admin berhasil dibuat. Silakan masuk.';
    } else {
        $flash = 'Gagal membuat akun. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleLogin(array $settings, string $csrf, bool $isAuthenticated, string &$flash): array
{
    if ($isAuthenticated) {
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

    if ($action !== 'login') {
        return $settings;
    }

    $password = $_POST['password'] ?? '';
    $storedHash = $settings['password_hash'] ?? '';

    if ($storedHash === '' || !password_verify($password, $storedHash)) {
        $flash = 'Password salah.';
        return $settings;
    }

    session_start();
    $_SESSION['pojok_berkah_admin'] = true;
    session_regenerate_id(true);

    return $settings;
}

function handleChangePassword(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'change_password') {
        return $settings;
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmation = $_POST['new_password_confirmation'] ?? '';

    $storedHash = $settings['password_hash'] ?? '';

    if (!password_verify($currentPassword, $storedHash)) {
        $flash = 'Password saat ini salah.';
        return $settings;
    }

    if (!isValidAdminPassword($newPassword)) {
        $flash = 'Password baru minimal 6 karakter dan mengandung huruf serta angka.';
        return $settings;
    }

    if ($newPassword !== $confirmation) {
        $flash = 'Konfirmasi password baru tidak cocok.';
        return $settings;
    }

    $settings['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
    if (saveSettings($storagePath, $settings)) {
        $flash = 'Password berhasil diganti.';
    } else {
        $flash = 'Gagal menyimpan password. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}

function handleLogout(bool $isAuthenticated): bool
{
    if (!$isAuthenticated) {
        return false;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }

    $action = $_POST['action'] ?? '';

    if ($action !== 'logout') {
        return false;
    }

    session_start();
    $_SESSION = [];
    session_destroy();

    return true;
}