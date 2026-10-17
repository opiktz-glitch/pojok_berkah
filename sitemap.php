<?php
declare(strict_types=1);

define('POJOK_BERKAH_BOOTSTRAPPED', true);

$storagePath = __DIR__ . '/data/storage.php';
$settings = is_file($storagePath) ? require $storagePath : [];
$articles = isset($settings['articles']) && is_array($settings['articles']) ? $settings['articles'] : [];

header('Content-Type: application/xml; charset=utf-8');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://pojokberkah.online/</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://pojokberkah.online/tentang-kami.html</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://pojokberkah.online/artikel/</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
<?php foreach ($articles as $article): 
    if (!is_array($article) || ($article['status'] ?? '') !== 'published' || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string)($article['slug'] ?? ''))) {
        continue;
    }
    $lastmod = (string)($article['updated_at'] ?? $article['created_at'] ?? date('Y-m-d'));
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $lastmod)) {
        $lastmod = date('Y-m-d');
    }
?>
  <url>
    <loc>https://pojokberkah.online/artikel/<?= htmlspecialchars(rawurlencode((string)$article['slug']), ENT_XML1, 'UTF-8') ?>.html</loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_XML1, 'UTF-8') ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
<?php endforeach; ?>
  <url>
    <loc>https://pojokberkah.online/kebijakan-privasi.html</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.4</priority>
  </url>
  <url>
    <loc>https://pojokberkah.online/syarat-ketentuan.html</loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq>yearly</changefreq>
    <priority>0.4</priority>
  </url>
</urlset>
