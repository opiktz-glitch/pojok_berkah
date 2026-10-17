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
    if (strpos($digits, '0') === 0) {
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

function slugify(string $text): string
{
    $text = trim($text);
    // Transliterate ke ASCII jika ada ext-iconv
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if (is_string($converted) && $converted !== '') {
            $text = $converted;
        }
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    if (!is_string($text)) {
        return '';
    }
    $text = trim($text, '-');
    $text = preg_replace('/-+/', '-', $text);
    if (!is_string($text)) {
        return '';
    }
    // Batasi 60 karakter, potong di batas kata (-)
    if (strlen($text) > 60) {
        $text = substr($text, 0, 60);
        $text = rtrim($text, '-');
        $pos = strrpos($text, '-');
        if ($pos !== false && $pos > 20) {
            $text = substr($text, 0, $pos);
        }
    }
    return $text;
}

function normalizeArticleSlug(string $value): ?string
{
    $slug = slugify($value);
    if ($slug === '' || strlen($slug) < 3) {
        return null;
    }
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        return null;
    }
    return $slug;
}

function uniqueArticleSlug(string $desired, array $existingSlugs, string $currentSlug = ''): string
{
    $base = normalizeArticleSlug($desired);
    if ($base === null) {
        $base = 'artikel';
    }
    // Jika edit dan slug tidak berubah, pakai slug lama
    if ($currentSlug !== '' && $base === $currentSlug) {
        return $currentSlug;
    }
    $slug = $base;
    $i = 2;
    while (in_array($slug, $existingSlugs, true)) {
        // Jika edit, abaikan slug sendiri
        if ($slug === $currentSlug) {
            break;
        }
        $slug = $base . '-' . $i;
        $i++;
        if ($i > 99) {
            $slug = $base . '-' . bin2hex(random_bytes(2));
            break;
        }
    }
    return $slug;
}