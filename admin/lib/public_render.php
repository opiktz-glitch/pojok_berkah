<?php
declare(strict_types=1);

if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

/**
 * Menerapkan data CMS (WhatsApp, gambar, logo, layanan, FAQ, artikel terbaru)
 * ke HTML halaman publik di SERVER, sehingga halaman tidak butuh JavaScript.
 *
 * Template HTML tetap berisi isi bawaan; fungsi ini hanya menimpa bagian
 * yang punya penanda data-* (data-image-slot, data-service-*, dst.).
 * Setiap langkah aman gagal: bila regex error, HTML bawaan dipertahankan.
 */

function pbEsc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** preg_replace_callback yang tidak pernah mengembalikan null. */
function pbReplace(string $pattern, callable $callback, string $subject, int $limit = -1): string
{
    $result = preg_replace_callback($pattern, $callback, $subject, $limit);
    return $result === null ? $subject : $result;
}

/** Set / tambah atribut pada satu tag HTML. */
function pbSetAttr(string $tag, string $name, string $value): string
{
    $quoted = pbEsc($value);
    $existing = '/(\s' . preg_quote($name, '/') . ')\s*=\s*("[^"]*"|\'[^\']*\')/i';
    if (preg_match($existing, $tag) === 1) {
        return pbReplace($existing, static function (array $m) use ($quoted): string {
            return $m[1] . '="' . $quoted . '"';
        }, $tag, 1);
    }
    return pbReplace('/\s*(\/?>)$/', static function (array $m) use ($name, $quoted): string {
        return ' ' . $name . '="' . $quoted . '"' . $m[1];
    }, $tag, 1);
}

function pbRemoveHidden(string $tag): string
{
    $result = preg_replace('/\shidden(?=[\s>\/])/i', '', $tag, 1);
    return $result === null ? $tag : $result;
}

function pbAddHidden(string $tag): string
{
    if (preg_match('/\shidden(?=[\s>\/])/i', $tag) === 1) {
        return $tag;
    }
    return pbReplace('/\s*(\/?>)$/', static function (array $m): string {
        return ' hidden' . $m[1];
    }, $tag, 1);
}

function pbIsSafeUrl(string $url): bool
{
    return preg_match('~^https?://[^\s"\'<>]+$~i', $url) === 1;
}

function pbIsSafeImage(string $src): bool
{
    return preg_match('~^(?:https?://[^\s"\'<>]+|data:image/(?:jpeg|png|webp|gif);base64,[A-Za-z0-9+/=]+|data/uploads/service_[a-z]+\.(?:jpg|png|webp)\?v=[a-f0-9]+)$~i', $src) === 1;
}

/** Gabungkan default dengan isi CMS (sama seperti endpoint ?public=1). */
function pbPublicData(array $settings): array
{
    $merge = static function (array $defaults, $custom): array {
        if (!is_array($custom)) {
            return $defaults;
        }
        foreach ($custom as $key => $value) {
            if (is_string($key) && is_string($value) && $value !== '') {
                $defaults[$key] = $value;
            }
        }
        return $defaults;
    };

    $names = $merge([
        'tryout' => 'Tryout TKA',
        'gadai' => 'Gadai BPKB',
        'kitchen' => 'Alfi Kitchen',
        'parkir' => 'Aplikasi Parkir',
    ], $settings['service_names'] ?? null);

    $urls = $merge([
        'tryout' => 'https://tryout.pojokberkah.online',
        'gadai' => 'https://gadai.pojokberkah.online',
        'kitchen' => 'https://kitchen.pojokberkah.online',
        'parkir' => 'https://parkir.pojokberkah.online',
    ], $settings['service_urls'] ?? null);

    $descriptions = $merge([
        'tryout' => 'Latihan soal dan simulasi ujian TKA untuk persiapan seleksi cpns.',
        'gadai' => 'Informasi simulasi gadai BPKB dengan estimasi nilai jaminan.',
        'kitchen' => 'Puding berlapis buah, dessert dalam kemasan praktis, dan salad buah bersaus creamy.',
        'parkir' => 'Sistem manajemen parkir digital untuk lingkungan perkantoran dan perumahan.',
    ], $settings['service_descriptions'] ?? null);

    $images = [
        'logo' => '',
        'tryout' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=960&q=80',
        'gadai' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=960&q=80',
        'kitchen' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=960&q=80',
        'parkir' => 'https://images.unsplash.com/photo-1449824903919-6a436d8f2099?auto=format&fit=crop&w=960&q=80',
    ];
    $storedImages = isset($settings['images']) && is_array($settings['images']) ? $settings['images'] : [];
    foreach ($images as $slot => $default) {
        $stored = $storedImages[$slot] ?? '';
        if (is_string($stored) && $stored !== '' && pbIsSafeImage($stored)) {
            $images[$slot] = $stored;
        }
    }

    $faq = [];
    if (isset($settings['faq']) && is_array($settings['faq'])) {
        foreach ($settings['faq'] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $question = trim((string)($item['question'] ?? ''));
            $answer = trim((string)($item['answer'] ?? ''));
            if ($question !== '' && $answer !== '') {
                $faq[] = ['question' => $question, 'answer' => $answer];
            }
        }
    }

    $articles = [];
    $stored = isset($settings['articles']) && is_array($settings['articles']) ? $settings['articles'] : [];
    foreach ($stored as $article) {
        if (!is_array($article) || ($article['status'] ?? '') !== 'published') {
            continue;
        }
        $slug = (string)($article['slug'] ?? '');
        $title = trim((string)($article['title'] ?? ''));
        $excerpt = trim((string)($article['excerpt'] ?? ''));
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) !== 1 || $title === '' || $excerpt === '') {
            continue;
        }
        $articles[] = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $excerpt,
            'updated_at' => (string)($article['updated_at'] ?? $article['created_at'] ?? ''),
            'image_url' => !empty($article['image'])
                ? 'admin.php?article_image=' . rawurlencode($slug) . '&v=' . substr(sha1((string)$article['image']), 0, 12)
                : '',
            'image_alt' => (string)($article['image_alt'] ?? ''),
        ];
    }
    usort($articles, static function (array $a, array $b): int {
        return strcmp($b['updated_at'], $a['updated_at']);
    });

    $waNumber = (string)($settings['wa_number'] ?? '');

    return [
        'wa_number' => preg_match('/^\d{8,15}$/', $waNumber) === 1 ? $waNumber : '',
        'names' => $names,
        'urls' => $urls,
        'descriptions' => $descriptions,
        'images' => $images,
        'faq' => $faq,
        'articles' => array_slice($articles, 0, 3),
    ];
}

function pbApplyYear(string $html): string
{
    return pbReplace('~(<span id="yr">)\d{4}(</span>)~', static function (array $m): string {
        return $m[1] . date('Y') . $m[2];
    }, $html);
}

function pbApplyWhatsApp(string $html, array $data): string
{
    if ($data['wa_number'] === '') {
        return $html;
    }
    return pbReplace('~(href="https://wa\.me/)\d*~i', static function (array $m) use ($data): string {
        return $m[1] . $data['wa_number'];
    }, $html);
}

function pbApplyImages(string $html, array $data): string
{
    $images = $data['images'];

    $html = pbReplace('~<img\b[^>]*\bdata-image-slot="([a-z]+)"[^>]*>~i', static function (array $m) use ($images): string {
        $source = $images[$m[1]] ?? '';
        if ($source === '') {
            return $m[0];
        }
        return pbRemoveHidden(pbSetAttr($m[0], 'src', $source));
    }, $html);

    if ($images['logo'] === '') {
        return $html;
    }

    $html = pbReplace('~<[a-z0-9]+\b[^>]*\bdata-logo-fallback\b[^>]*>~i', static function (array $m): string {
        return pbAddHidden($m[0]);
    }, $html);

    return pbReplace('~<link\b[^>]*\brel="icon"[^>]*>~i', static function (array $m) use ($images): string {
        return pbSetAttr($m[0], 'href', $images['logo']);
    }, $html, 1);
}

function pbApplyServices(string $html, array $data): string
{
    // Tautan layanan
    $html = pbReplace('~<a\b[^>]*\bdata-service-link="([a-z]+)"[^>]*>~i', static function (array $m) use ($data): string {
        $url = $data['urls'][$m[1]] ?? '';
        return pbIsSafeUrl($url) ? pbSetAttr($m[0], 'href', $url) : $m[0];
    }, $html);

    // Nama dan deskripsi layanan (hanya elemen yang isinya teks murni)
    $textFields = ['data-service-name' => 'names', 'data-service-description' => 'descriptions'];
    foreach ($textFields as $attribute => $key) {
        $pattern = '~(<([a-z0-9]+)\b[^>]*\b' . preg_quote($attribute, '~') . '="([a-z]+)"[^>]*>)([^<]*)(</\2>)~i';
        $html = pbReplace($pattern, static function (array $m) use ($data, $key): string {
            $value = $data[$key][$m[3]] ?? '';
            return $value === '' ? $m[0] : $m[1] . pbEsc($value) . $m[5];
        }, $html);
    }
    return $html;
}

function pbApplyArticles(string $html, array $data): string
{
    if ($data['articles'] === []) {
        return $html;
    }

    $cards = '';
    foreach ($data['articles'] as $article) {
        $url = 'artikel/' . rawurlencode($article['slug']) . '.html';
        $cards .= '<article class="latest-article-card reveal">';
        if ($article['image_url'] !== '') {
            $cards .= '<img class="latest-article-cover" src="' . pbEsc($article['image_url']) . '" alt="'
                . pbEsc($article['image_alt']) . '" loading="lazy" decoding="async">';
        }
        $cards .= '<h3><a href="' . pbEsc($url) . '">' . pbEsc($article['title']) . '</a></h3>'
            . '<p>' . pbEsc($article['excerpt']) . '</p>'
            . '<a class="card-link" href="' . pbEsc($url) . '">Baca artikel</a></article>';
    }

    $filled = pbReplace('~(<div class="latest-article-grid" id="latest-article-list">)</div>~', static function (array $m) use ($cards): string {
        return $m[1] . $cards . '</div>';
    }, $html, 1);
    if ($filled === $html) {
        return $html;
    }

    return pbReplace('~<section\b[^>]*\bid="artikel"[^>]*>~i', static function (array $m): string {
        return pbRemoveHidden($m[0]);
    }, $filled, 1);
}

function pbApplyFaq(string $html, array $data): string
{
    // Tanpa item valid dari CMS, FAQ bawaan HTML dipertahankan.
    if ($data['faq'] === []) {
        return $html;
    }

    $items = '';
    $faqEntities = [];
    foreach ($data['faq'] as $item) {
        $items .= '<details><summary>' . pbEsc($item['question']) . '</summary><p>' . pbEsc($item['answer']) . '</p></details>';
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['answer'],
            ],
        ];
    }

    $html = pbReplace('~(<div class="faq[^"]*">)(?:(?!</div>).)*(</div>)~s', static function (array $m) use ($items): string {
        return $m[1] . $items . $m[2];
    }, $html, 1);

    // Sinkronkan juga schema FAQPage JSON-LD di <head> agar data terstruktur tetap cocok dengan konten
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqEntities,
    ];
    $jsonLd = json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if (is_string($jsonLd)) {
        $html = pbReplace('~<script type="application/ld\+json">\{"@context":"https://schema\.org","@type":"FAQPage",.*?</script>~s', static function () use ($jsonLd): string {
            return '<script type="application/ld+json">' . $jsonLd . '</script>';
        }, $html, 1);
    }

    return $html;
}

/** Titik masuk: terima HTML template + settings, kembalikan HTML final. */
function pbRenderPublicPage(string $html, array $settings): string
{
    try {
        $data = pbPublicData($settings);
        $html = pbApplyYear($html);
        $html = pbApplyWhatsApp($html, $data);
        $html = pbApplyImages($html, $data);
        $html = pbApplyServices($html, $data);
        $html = pbApplyArticles($html, $data);
        $html = pbApplyFaq($html, $data);
    } catch (Throwable $error) {
        // Jangan pernah merusak halaman publik: tampilkan HTML apa adanya.
    }
    return $html;
}
