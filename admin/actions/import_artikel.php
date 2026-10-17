<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';

/**
 * Membaca file artikel (HTML, Markdown, atau teks) di server lalu menyimpan
 * hasilnya sebagai draf sementara di sesi. Formulir artikel menampilkan draf
 * itu agar bisa ditinjau sebelum benar-benar disimpan. Tidak butuh JavaScript.
 */

function importCleanMarkdown(string $text): string
{
    $text = preg_replace_callback('/```[\s\S]*?```/', static function (array $m): string {
        $block = preg_replace('/^```[^\n]*\n?/', '', $m[0]);
        $block = preg_replace('/```$/', '', (string)$block);
        return (string)$block;
    }, $text) ?? $text;
    $rules = [
        '/^\s{0,3}#{1,6}\s+/m' => '',
        '/^\s*[-*+]\s+/m' => '• ',
        '/^\s*\d+\.\s+/m' => '• ',
        '/!?\[([^\]]+)\]\([^)]+\)/' => '$1',
        '/[*_~]{1,3}([^*_~]+)[*_~]{1,3}/' => '$1',
        '/^>\s?/m' => '',
    ];
    foreach ($rules as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text) ?? $text;
    }
    return trim($text);
}

/** @return array{title:string,excerpt:string,content:string} */
function importParseHtml(string $raw): array
{
    $previous = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $raw, LIBXML_NOWARNING | LIBXML_NOERROR);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($dom);

    $titleNode = $xpath->query('//h1')->item(0) ?? $xpath->query('//title')->item(0);
    $descNode = $xpath->query('//meta[@name="description"]/@content')->item(0);
    $source = $xpath->query('//main')->item(0) ?? $xpath->query('//article')->item(0) ?? $xpath->query('//body')->item(0);
    if (!$source instanceof DOMNode) {
        return ['title' => '', 'excerpt' => '', 'content' => ''];
    }

    $drop = $xpath->query('.//script|.//style|.//noscript|.//*[local-name()="svg"]|.//nav|.//footer|.//header|.//button|.//form', $source);
    if ($drop !== false) {
        foreach (iterator_to_array($drop) as $node) {
            if ($node->parentNode !== null) {
                $node->parentNode->removeChild($node);
            }
        }
    }
    $breaks = $xpath->query('.//br', $source);
    if ($breaks !== false) {
        foreach (iterator_to_array($breaks) as $node) {
            if ($node->parentNode !== null) {
                $node->parentNode->replaceChild($dom->createTextNode("\n"), $node);
            }
        }
    }
    $blocks = $xpath->query('.//h1|.//h2|.//h3|.//h4|.//p|.//li|.//div|.//section|.//article|.//details|.//summary', $source);
    if ($blocks !== false) {
        foreach (iterator_to_array($blocks) as $node) {
            if ($node->parentNode !== null) {
                $node->parentNode->insertBefore($dom->createTextNode("\n"), $node);
                $node->parentNode->insertBefore($dom->createTextNode("\n"), $node->nextSibling);
            }
        }
    }

    $content = (string)$source->textContent;
    $content = preg_replace('/[\t ]+\n/', "\n", $content) ?? $content;
    $content = preg_replace('/\n[\t ]+/', "\n", $content) ?? $content;
    $content = trim(preg_replace('/\n{3,}/', "\n\n", $content) ?? $content);

    $summary = $descNode instanceof DOMNode ? trim((string)$descNode->nodeValue) : '';
    $paragraphs = preg_split('/\n\s*\n/', $content) ?: [];

    return [
        'title' => $titleNode instanceof DOMNode ? trim((string)$titleNode->textContent) : '',
        'excerpt' => $summary !== '' ? $summary : (string)($paragraphs[0] ?? ''),
        'content' => $content,
    ];
}

/** @return array{title:string,excerpt:string,content:string} */
function importParseText(string $raw, string $extension): array
{
    // Cari judul "# Judul" SEBELUM markdown dibersihkan (tanda # akan hilang).
    $lines = preg_split('/\r?\n/', $raw) ?: [];
    $title = '';
    foreach ($lines as $index => $line) {
        if (preg_match('/^\s*#\s+(.+)$/', $line, $match) === 1) {
            $title = trim($match[1]);
            unset($lines[$index]);
            break;
        }
    }
    $body = implode("\n", $lines);
    $content = in_array($extension, ['md', 'markdown'], true) ? importCleanMarkdown($body) : trim($body);
    $paragraphs = preg_split('/\n\s*\n/', $content) ?: [];

    return ['title' => $title, 'excerpt' => (string)($paragraphs[0] ?? ''), 'content' => $content];
}

function handleImportArtikel(array $settings, string $csrf, bool $isAuthenticated, string &$flash): array
{
    if (!$isAuthenticated || ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return $settings;
    }
    if (($_POST['action'] ?? '') !== 'import_artikel') {
        return $settings;
    }
    if (!verifyCsrf((string)($_POST['csrf'] ?? ''), $csrf)) {
        http_response_code(403);
        exit;
    }

    $file = $_FILES['artikel_file'] ?? null;
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        || !is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
        $flash = 'Pilih file artikel terlebih dahulu.';
        return $settings;
    }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        $flash = 'File melebihi batas 2 MB.';
        return $settings;
    }

    $originalName = (string)($file['name'] ?? '');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, ['html', 'htm', 'md', 'markdown', 'txt'], true)) {
        $flash = 'Format file harus HTML, MD, atau TXT.';
        return $settings;
    }

    $raw = file_get_contents((string)$file['tmp_name']);
    if ($raw === false || $raw === '') {
        $flash = 'File tidak bisa dibaca. Coba pilih file lain.';
        return $settings;
    }
    // Buang BOM dan pastikan UTF-8 valid.
    $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;
    $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-8');

    $looksLikeHtml = in_array($extension, ['html', 'htm'], true)
        || preg_match('/^\s*<!doctype html|^\s*<html/i', $raw) === 1;
    $parsed = $looksLikeHtml ? importParseHtml($raw) : importParseText($raw, $extension);

    $filenameTitle = trim(preg_replace('/[_-]+/', ' ', (string)pathinfo($originalName, PATHINFO_FILENAME)) ?? '');
    $title = $parsed['title'] !== '' ? $parsed['title'] : $filenameTitle;
    $excerpt = trim(preg_replace('/\s+/', ' ', $parsed['excerpt']) ?? '');

    $_SESSION['artikel_draft'] = [
        'title' => mb_substr($title, 0, 120),
        'excerpt' => mb_substr($excerpt, 0, 320),
        'content' => mb_substr($parsed['content'], 0, 30000),
    ];
    $flash = 'Isi file dimasukkan ke formulir di bawah. Periksa judul, ringkasan, dan isi sebelum menyimpan.';

    return $settings;
}
