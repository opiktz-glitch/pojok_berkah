<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

function saveSettings(string $path, array $settings): bool
{
    $content = "<?php\nif (!defined('POJOK_BERKAH_BOOTSTRAPPED')) { http_response_code(404); exit; }\nreturn " . var_export($settings, true) . ";\n";
    return file_put_contents($path, $content, LOCK_EX) !== false;
}