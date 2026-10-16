<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/storage.php';

function handleSaveFaq(array $settings, string $storagePath, string $csrf, bool $isAuthenticated, string &$flash): array
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

    if ($action !== 'save_faq') {
        return $settings;
    }

    $questions = $_POST['faq_questions'] ?? [];
    $answers = $_POST['faq_answers'] ?? [];

    $faq = [];
    $maxItems = min(count($questions), count($answers), 20);

    for ($i = 0; $i < $maxItems; $i++) {
        $question = trim($questions[$i] ?? '');
        $answer = trim($answers[$i] ?? '');
        if ($question !== '' && $answer !== '') {
            $faq[] = ['question' => $question, 'answer' => $answer];
        }
    }

    if (count($faq) === 0) {
        $flash = 'Minimal satu pertanyaan dan jawaban wajib diisi.';
        return $settings;
    }

    $settings['faq'] = $faq;
    if (saveSettings($storagePath, $settings)) {
        $flash = 'FAQ berhasil disimpan.';
    } else {
        $flash = 'FAQ tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
    }

    return $settings;
}