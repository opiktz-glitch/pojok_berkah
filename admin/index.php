<?php
declare(strict_types=1);

define('POJOK_BERKAH_BOOTSTRAPPED', true);

session_start();

$storagePath = __DIR__ . '/../data/storage.php';
$settings = is_file($storagePath) ? require $storagePath : [];
if (!is_array($settings)) {
    $settings = ['wa_number' => '6287724039666', 'password_hash' => ''];
}

require_once __DIR__ . '/lib/security.php';
require_once __DIR__ . '/lib/normalize.php';
require_once __DIR__ . '/lib/storage.php';
require_once __DIR__ . '/actions/login.php';
require_once __DIR__ . '/actions/save.php';
require_once __DIR__ . '/actions/save_service.php';
require_once __DIR__ . '/actions/upload.php';
require_once __DIR__ . '/actions/save_faq.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];
$flash = '';

$setupRequired = empty($settings['password_hash']);
$canSetup = ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' || ($_SERVER['REMOTE_ADDR'] ?? '') === '::1';
$isAuthenticated = !empty($_SESSION['pojok_berkah_admin'] ?? false);

$settings = handleSetup($settings, $storagePath, $csrf, $setupRequired, $canSetup, $flash);
$settings = handleLogin($settings, $csrf, $isAuthenticated, $flash);
$settings = handleChangePassword($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveWhatsApp($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveStats($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveServiceNames($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveServiceUrls($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveServiceDescriptions($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveServiceTags($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleUploadImage($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleRemoveImage($settings, $storagePath, $csrf, $isAuthenticated, $flash);
$settings = handleSaveFaq($settings, $storagePath, $csrf, $isAuthenticated, $flash);

$logout = handleLogout($isAuthenticated);
if ($logout) {
    header('Location: admin.php');
    exit;
}

$isAuthenticated = !empty($_SESSION['pojok_berkah_admin'] ?? false);

$waNumber = $settings['wa_number'] ?? '6287724039666';
$serviceNames = $settings['service_names'] ?? [
    'tryout' => 'Tryout TKA',
    'gadai' => 'Gadai BPKB',
    'kitchen' => 'Alfi Kitchen',
    'parkir' => 'Aplikasi Parkir',
];
$serviceUrls = $settings['service_urls'] ?? [
    'tryout' => 'https://tryout.pojokberkah.online',
    'gadai' => 'https://gadai.pojokberkah.online',
    'kitchen' => 'https://kitchen.pojokberkah.online',
    'parkir' => 'https://parkir.pojokberkah.online',
];
$serviceDescriptions = $settings['service_descriptions'] ?? [
    'tryout' => 'Latihan soal dan simulasi ujian TKA untuk persiapan seleksi cpns.',
    'gadai' => 'Informasi simulasi gadai BPKB dengan estimasi nilai jaminan.',
    'kitchen' => 'Puding berlapis buah, dessert dalam kemasan praktis, dan salad buah bersaus creamy.',
    'parkir' => 'Sistem manajemen parkir digital untuk lingkungan perkantoran dan perumahan.',
];
$serviceTags = $settings['service_tags'] ?? [
    'tryout' => ['TKA', 'CPNS', 'Seleksi'],
    'gadai' => ['BPKB', 'Gadai', 'Simulasi'],
    'kitchen' => ['Dessert', 'Buatan Rumahan', 'Alfi Kitchen'],
    'parkir' => ['Parkir', 'Digital', 'Sistem'],
];
$stats = $settings['stats'] ?? [
    'services' => ['value' => '3', 'label' => 'layanan'],
    'gadai' => ['value' => '29', 'label' => 'cabang gadai aktif'],
    'kitchen' => ['value' => 'Always Fresh', 'label' => 'komitmen Alfi Kitchen'],
];
$faqItems = $settings['faq'] ?? [
    ['question' => 'Apa itu Pojok Berkah?', 'answer' => 'Pojok Berkah adalah ruang tumbuh untuk belajar dan usaha. Kami mengelola beberapa inisiatif melalui unit-unit khusus dengan standar yang sama.'],
    ['question' => 'Bagaimana cara menghubungi admin?', 'answer' => 'Hubungi kami melalui tombol WhatsApp di halaman ini atau email admin@pojokberkah.online.'],
    ['question' => 'Apakah saya perlu membuat akun untuk melihat informasi?', 'answer' => 'Tidak. Informasi di halaman ini dapat dibaca langsung tanpa membuat akun.'],
    ['question' => 'Di mana saya bisa membaca informasi resmi?', 'answer' => 'Lihat halaman Tentang Kami, Kebijakan Privasi, serta Syarat dan Ketentuan yang tertaut di footer.'],
];
$imageSlots = [
    'logo' => 'Logo situs',
    'tryout' => 'Tryout TKA',
    'gadai' => 'Gadai BPKB',
    'kitchen' => 'Alfi Kitchen',
    'parkir' => 'Aplikasi Parkir',
];
$defaultImageUrls = [
    'logo' => '',
    'tryout' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=960&q=80',
    'gadai' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=960&q=80',
    'kitchen' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=960&q=80',
    'parkir' => 'https://images.unsplash.com/photo-1449824903919-6a436d8f2099?auto=format&fit=crop&w=960&q=80',
];
$imageSources = [];
foreach ($imageSlots as $slot => $label) {
    $stored = $settings['images'][$slot] ?? '';
    if ($stored !== '' && is_string($stored)) {
        $imageSources[$slot] = $stored;
    } else {
        $imageSources[$slot] = $defaultImageUrls[$slot] ?? '';
    }
}

// Public JSON endpoint untuk halaman frontend (contoh: tentang-kami.html)
if ((isset($_GET['public']) && $_GET['public'] === '1')
    || (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] === 'public=1')) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode([
        'wa_number' => $waNumber,
        'service_names' => $serviceNames,
        'service_urls' => $serviceUrls,
        'service_descriptions' => $serviceDescriptions,
        'service_tags' => $serviceTags,
        'images' => $imageSources,
        'stats' => $stats,
    ]);
    exit;
}

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#F4F8FC">
<title>CMS | Pojok Berkah</title>
<link rel="canonical" href="https://pojokberkah.online/admin.php">
<meta name="robots" content="noindex,nofollow,noarchive">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
<link rel="icon" href="/assets/icons/favicon.ico" sizes="any">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-touch-icon.png">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/pojok_berkah/assets/css/admin.css">
<style>
@media(max-width:420px){.wrap{width:min(100% - 28px,1040px)}.top nav{font-size:.84rem}.panel{padding:18px}}
</style>
</head>
<body>
<header>
  <div class="wrap top">
    <a class="brand" href="index.html"><?php if ($imageSources['logo'] !== ''): ?><img class="brand-image" src="<?= escapeHtml($imageSources['logo']) ?>" alt=""><?php else: ?><span class="mark">P</span><?php endif; ?>Pojok Berkah <span style="font-family:var(--body);font-size:.78rem;font-weight:500;color:var(--muted)">/ CMS</span></a>
    <nav aria-label="Navigasi"><a href="index.html">Lihat situs</a><a href="tentang-kami.html">Tentang kami</a></nav>
  </div>
</header>
<main class="wrap">
  <span class="kicker">Pengaturan situs</span>
  <h1>Pengaturan situs</h1>
  <p class="lead">Kelola nomor WhatsApp, logo, dan pertanyaan umum. Konten & gambar layanan di bawah digunakan di halaman Karya, Tentang Kami, dan subdomain.</p>

  <?php if ($flash !== ''): ?>
    <p class="flash" role="status"><?= escapeHtml($flash) ?></p>
  <?php endif; ?>

  <?php if ($setupRequired && $canSetup): ?>
    <section class="panel">
      <div>
        <?php include __DIR__ . '/views/setup.php'; ?>
      </div>
    </section>
  <?php elseif ($setupRequired): ?>
    <section class="panel">
      <div>
        <h2>Setup awal perlu dilakukan di server</h2>
        <p class="form-copy">Buka halaman ini dari komputer yang menjalankan PHP untuk membuat akun admin. Akses setup dari jaringan luar ditolak.</p>
      </div>
    </section>
  <?php elseif (!$isAuthenticated): ?>
    <section class="panel">
      <div>
        <?php include __DIR__ . '/views/login-form.php'; ?>
      </div>
    </section>
  <?php else: ?>
    <nav class="tabs" aria-label="Menu pengaturan">
      <a href="#tab-whatsapp" class="tab-link tab-active">WhatsApp</a>
      <a href="#tab-logo" class="tab-link">Logo</a>
      <a href="#tab-faq" class="tab-link">FAQ</a>
    </nav>

    <div class="tab-panels">
      <section id="tab-whatsapp" class="tab-panel tab-active">
        <div><?php include __DIR__ . '/views/tab-whatsapp.php'; ?></div>
      </section>

      <section id="tab-logo" class="tab-panel">
        <div><?php include __DIR__ . '/views/tab-logo.php'; ?></div>
      </section>

      <section id="tab-faq" class="tab-panel">
        <div><?php include __DIR__ . '/views/tab-faq.php'; ?></div>
      </section>
    </div>

    <div class="archive" style="border:none;background:transparent;margin-top:0;">
      <div class="archive-body">
        <section id="tab-password" class="tab-panel">
          <div><?php include __DIR__ . '/views/tab-password.php'; ?></div>
        </section>
      </div>
    </div>

    <details class="archive">
      <summary>Layanan — Arsip — tidak tampil di landing, tetap tersimpan untuk Tentang Kami/subdomain</summary>
      <div class="archive-body">
        <section id="tab-layanan" class="tab-panel">
          <div><?php include __DIR__ . '/views/tab-layanan.php'; ?></div>
        </section>
      </div>
    </details>

    <details class="archive">
      <summary>Statistik — Arsip — tidak tampil di landing, tetap tersimpan untuk Tentang Kami/subdomain</summary>
      <div class="archive-body">
        <section id="tab-statistik" class="tab-panel">
          <div><?php include __DIR__ . '/views/tab-statistik.php'; ?></div>
        </section>
      </div>
    </details>

    <details class="archive">
      <summary>Gambar arsip (dipakai Tentang Kami dan 404)</summary>
      <div class="archive-body">
        <section id="tab-gambar" class="tab-panel">
          <div><?php include __DIR__ . '/views/tab-gambar.php'; ?></div>
        </section>
      </div>
    </details>
  <?php endif; ?>
</main>
<footer class="wrap">CMS Pojok Berkah</footer>
<script>
(function(){
  var links=document.querySelectorAll('.tab-link');
  var panels=document.querySelectorAll('.tab-panel');
  links.forEach(function(link){
    link.addEventListener('click',function(e){
      e.preventDefault();
      var target=this.getAttribute('href').slice(1);
      links.forEach(function(l){l.classList.remove('tab-active');});
      panels.forEach(function(p){p.classList.remove('tab-active');});
      this.classList.add('tab-active');
      document.getElementById(target).classList.add('tab-active');
    });
  });
})();

var faqList=document.getElementById("faq-items");
if(faqList){
  document.getElementById("add-faq").addEventListener("click",function(){
    if(faqList.children.length>=20)return;
    var item=document.createElement("div");item.className="faq-item";
    var questionLabel=document.createElement("label");questionLabel.textContent="Pertanyaan";
    var question=document.createElement("input");question.name="faq_questions[]";question.maxLength=160;question.required=true;questionLabel.appendChild(question);
    var answerLabel=document.createElement("label");answerLabel.textContent="Jawaban";
    var answer=document.createElement("textarea");answer.name="faq_answers[]";answer.maxLength=2000;answer.required=true;answerLabel.appendChild(answer);
    var remove=document.createElement("button");remove.className="remove-faq";remove.type="button";remove.textContent="Hapus";
    item.append(questionLabel,answerLabel,remove);faqList.appendChild(item);question.focus();
  });
  faqList.addEventListener("click",function(event){
    if(event.target.matches(".remove-faq"))event.target.closest(".faq-item").remove();
  });
}
</script>
</body>
</html>