<?php
declare(strict_types=1);

/**
 * Menyajikan halaman publik (index, tentang-kami, dst.) dengan data CMS
 * yang sudah diterapkan di server. URL tetap *.html lewat .htaccess,
 * jadi canonical, sitemap, dan SEO tidak berubah.
 */
define('POJOK_BERKAH_BOOTSTRAPPED', true);

$pages = [
    'index' => 'index.html',
    'tentang-kami' => 'tentang-kami.html',
    'kebijakan-privasi' => 'kebijakan-privasi.html',
    'syarat-ketentuan' => 'syarat-ketentuan.html',
    '404' => '404.html',
];

$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : '';
if (!isset($pages[$page])) {
    http_response_code(404);
    exit;
}

$template = __DIR__ . '/' . $pages[$page];
$html = is_file($template) ? file_get_contents($template) : false;
if ($html === false) {
    http_response_code(404);
    exit;
}

$storagePath = __DIR__ . '/data/storage.php';
$settings = is_file($storagePath) ? require $storagePath : [];
if (!is_array($settings)) {
    $settings = [];
}

require_once __DIR__ . '/admin/lib/public_render.php';

if ($page === '404') {
    http_response_code(404);
}
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache');
echo pbRenderPublicPage($html, $settings);
