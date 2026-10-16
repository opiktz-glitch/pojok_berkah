<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

function normalizeWhatsAppNumber(string $value): ?string
{
    $digits = preg_replace('/\D+/', '', trim($value));
    if ($digits === null || $digits === '') {
        return null;
    }
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    }
    if (strlen($digits) < 8 || strlen($digits) > 15) {
        return null;
    }
    return $digits;
}

function normalizeServiceUrl(string $value): ?string
{
    $value = trim($value);
    $parts = parse_url($value);
    if ($value === '' || strlen($value) > 2048 || filter_var($value, FILTER_VALIDATE_URL) === false || !is_array($parts)) {
        return null;
    }
    if (!in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
        return null;
    }
    return $value;
}