<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

function csrfToken(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function escapeHtml($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isValidAdminPassword(string $password): bool
{
    return strlen($password) >= 6
        && strlen($password) <= 72
        && preg_match('/[A-Za-z]/', $password) === 1
        && preg_match('/[0-9]/', $password) === 1;
}

function verifyCsrf(string $submitted, string $expected): bool
{
    return hash_equals($expected, $submitted);
}

function checkLoginRateLimit(string &$flash): bool
{
    $attempts = $_SESSION['login_attempts'] ?? 0;
    $lastAttempt = $_SESSION['login_last_attempt'] ?? 0;
    $lockUntil = $_SESSION['login_lock_until'] ?? 0;

    if ($lockUntil > time()) {
        $remaining = (int)ceil(($lockUntil - time()) / 60);
        $flash = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . $remaining . ' menit.';
        return false;
    }

    if ($lastAttempt > 0 && (time() - $lastAttempt) > 900) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_lock_until'] = 0;
    }

    return true;
}

function recordLoginAttempt(bool $success): void
{
    if ($success) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['login_lock_until'] = 0;
        $_SESSION['login_last_attempt'] = 0;
        return;
    }

    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_last_attempt'] = time();

    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_lock_until'] = time() + 900;
    }
}