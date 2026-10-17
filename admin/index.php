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
$defaultServiceNames = [
    'tryout' => 'Tryout TKA',
    'gadai' => 'Gadai BPKB',
    'kitchen' => 'Alfi Kitchen',
    'parkir' => 'Aplikasi Parkir',
];
$serviceNames = array_merge($defaultServiceNames, isset($settings['service_names']) && is_array($settings['service_names']) ? $settings['service_names'] : []);
$defaultServiceUrls = [
    'tryout' => 'https://tryout.pojokberkah.online',
    'gadai' => 'https://gadai.pojokberkah.online',
    'kitchen' => 'https://kitchen.pojokberkah.online',
    'parkir' => 'https://parkir.pojokberkah.online',
];
$serviceUrls = array_merge($defaultServiceUrls, isset($settings['service_urls']) && is_array($settings['service_urls']) ? $settings['service_urls'] : []);
$defaultServiceDescriptions = [
    'tryout' => 'Latihan soal dan simulasi ujian TKA untuk persiapan seleksi cpns.',
    'gadai' => 'Informasi simulasi gadai BPKB dengan estimasi nilai jaminan.',
    'kitchen' => 'Puding berlapis buah, dessert dalam kemasan praktis, dan salad buah bersaus creamy.',
    'parkir' => 'Sistem manajemen parkir digital untuk lingkungan perkantoran dan perumahan.',
];
$serviceDescriptions = array_merge($defaultServiceDescriptions, isset($settings['service_descriptions']) && is_array($settings['service_descriptions']) ? $settings['service_descriptions'] : []);
$defaultServiceTags = [
    'tryout' => ['TKA', 'CPNS', 'Seleksi'],
    'gadai' => ['BPKB', 'Gadai', 'Simulasi'],
    'kitchen' => ['Dessert', 'Buatan Rumahan', 'Alfi Kitchen'],
    'parkir' => ['Parkir', 'Digital', 'Sistem'],
];
$rawServiceTags = isset($settings['service_tags']) && is_array($settings['service_tags']) ? $settings['service_tags'] : [];
$serviceTags = $defaultServiceTags;
foreach ($rawServiceTags as $slot => $tags) {
    $serviceTags[$slot] = is_array($tags) ? $tags : [];
}
$defaultStats = [
    'services' => ['value' => '3', 'label' => 'layanan'],
    'gadai' => ['value' => '29', 'label' => 'cabang gadai aktif'],
    'kitchen' => ['value' => 'Always Fresh', 'label' => 'komitmen Alfi Kitchen'],
];
$rawStats = isset($settings['stats']) && is_array($settings['stats']) ? $settings['stats'] : [];
$stats = $defaultStats;
foreach ($rawStats as $slot => $row) {
    if (is_array($row)) {
        $stats[$slot] = [
            'value' => $row['value'] ?? ($defaultStats[$slot]['value'] ?? ''),
            'label' => $row['label'] ?? ($defaultStats[$slot]['label'] ?? ''),
        ];
    }
}
$faqItems = $settings['faq'] ?? [
    ['question' => 'Apa itu Pojok Berkah?', 'answer' => 'Pojok Berkah adalah ruang tumbuh untuk belajar dan usaha. Kami mengelola beberapa inisiatif melalui unit-unit khusus dengan standar yang sama.'],
    ['question' => 'Bagaimana cara menghubungi admin?', 'answer' => 'Hubungi kami melalui tombol WhatsApp di halaman ini atau email admin@pojokberkah.online.'],
    ['question' => 'Apakah saya perlu membuat akun untuk melihat informasi?', 'answer' => 'Tidak. Informasi di halaman ini dapat dibaca langsung tanpa membuat akun.'],
    ['question' => 'Di mana saya bisa membaca informasi resmi?', 'answer' => 'Lihat halaman Tentang Kami, Kebijakan Privasi, serta Syarat dan Ketentuan yang tertaut di footer.'],
];
if (!is_array($faqItems)) {
    $faqItems = [];
} else {
    $cleanFaq = [];
    foreach ($faqItems as $item) {
        if (!is_array($item)) {
            continue;
        }
        $cleanFaq[] = [
            'question' => isset($item['question']) ? (string)$item['question'] : '',
            'answer' => isset($item['answer']) ? (string)$item['answer'] : '',
        ];
    }
    $faqItems = $cleanFaq;
}
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

// Tab aktif tanpa JS: via ?tab= (full reload). JS hanya enhancement.
$validTabs = ['whatsapp', 'logo', 'faq', 'password', 'layanan', 'statistik', 'gambar'];
$activeTab = 'whatsapp';
if (isset($_GET['tab']) && is_string($_GET['tab']) && in_array($_GET['tab'], $validTabs, true)) {
    $activeTab = $_GET['tab'];
}
// Setelah simpan (POST), tetap di tab yang relevan tanpa perlu input hidden.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save' || $postAction === 'logout') {
        $activeTab = 'whatsapp';
    } elseif ($postAction === 'save_faq') {
        $activeTab = 'faq';
    } elseif ($postAction === 'change_password') {
        $activeTab = 'password';
    } elseif ($postAction === 'save_stats') {
        $activeTab = 'statistik';
    } elseif (strpos((string)$postAction, 'save_service_') === 0) {
        $activeTab = 'layanan';
    } elseif ($postAction === 'upload_image' || $postAction === 'remove_image') {
        $slot = $_POST['image_slot'] ?? '';
        $activeTab = ($slot === 'logo') ? 'logo' : 'gambar';
    }
}
// Baris kosong tambahan untuk tambah FAQ tanpa JS (via ?faq_extra=N, hanya saat GET).
$faqExtra = 0;
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' && isset($_GET['faq_extra'])) {
    $faqExtra = max(0, (int)$_GET['faq_extra']);
}
$faqExtra = min($faqExtra, max(0, 20 - count($faqItems)));
if ($faqExtra > 0) {
    if ($activeTab === 'whatsapp') {
        $activeTab = 'faq';
    }
    for ($i = 0; $i < $faqExtra; $i++) {
        $faqItems[] = ['question' => '', 'answer' => ''];
    }
}

// Blokir index untuk endpoint gambar juga (Opsi B — robots izinkan crawl, noindex via header)
if (isset($_GET['image']) && is_string($_GET['image']) && $_GET['image'] !== '') {
    header('X-Robots-Tag: noindex, nofollow, noarchive');
}

// Public JSON endpoint untuk semua halaman publik (index.html, tentang-kami.html, 404.html)
// Opsi B robots.txt: endpoint publik diizinkan crawl tapi tetap noindex via header
if ((isset($_GET['public']) && $_GET['public'] === '1')
    || (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] === 'public=1')) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    $publicFaq = [];
    foreach ($faqItems as $item) {
        if (!is_array($item)) {
            continue;
        }
        $q = trim((string)($item['question'] ?? ''));
        $a = trim((string)($item['answer'] ?? ''));
        if ($q !== '' && $a !== '') {
            $publicFaq[] = ['question' => (string)($item['question'] ?? ''), 'answer' => (string)($item['answer'] ?? '')];
        }
    }
    echo json_encode([
        'wa_number' => $waNumber,
        'faq' => $publicFaq,
        'service_names' => $serviceNames,
        'service_urls' => $serviceUrls,
        'service_descriptions' => $serviceDescriptions,
        'service_tags' => $serviceTags,
        'images' => $imageSources,
        'stats' => $stats,
    ]);
    exit;
}

// Opsi B: admin HTML boleh di-crawl (robots.txt tidak blok) tapi wajib noindex via header + meta
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#F4F8FC">
<title>CMS | Pojok Berkah</title>
<meta name="robots" content="noindex,nofollow,noarchive">
<link rel="icon" type="image/png" sizes="32x32" href="assets/icons/favicon-32x32.png">
<link rel="icon" href="assets/icons/favicon.ico" sizes="any">
<link rel="apple-touch-icon" sizes="180x180" href="assets/icons/apple-touch-icon.png">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/admin.css">
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
      <a href="admin.php?tab=whatsapp" data-tab="whatsapp" class="tab-link<?= $activeTab === 'whatsapp' ? ' tab-active' : '' ?>">WhatsApp</a>
      <a href="admin.php?tab=logo" data-tab="logo" class="tab-link<?= $activeTab === 'logo' ? ' tab-active' : '' ?>">Logo</a>
      <a href="admin.php?tab=faq" data-tab="faq" class="tab-link<?= $activeTab === 'faq' ? ' tab-active' : '' ?>">FAQ</a>
      <a href="admin.php?tab=password" data-tab="password" class="tab-link tab-archive<?= $activeTab === 'password' ? ' tab-active' : '' ?>">Password</a>
      <a href="admin.php?tab=layanan" data-tab="layanan" class="tab-link tab-archive<?= $activeTab === 'layanan' ? ' tab-active' : '' ?>">Layanan</a>
      <a href="admin.php?tab=statistik" data-tab="statistik" class="tab-link tab-archive<?= $activeTab === 'statistik' ? ' tab-active' : '' ?>">Statistik</a>
      <a href="admin.php?tab=gambar" data-tab="gambar" class="tab-link tab-archive<?= $activeTab === 'gambar' ? ' tab-active' : '' ?>">Gambar arsip</a>
    </nav>

    <div class="tab-panels">
      <section id="tab-whatsapp" class="tab-panel<?= $activeTab === 'whatsapp' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-whatsapp.php'; ?></div>
      </section>

      <section id="tab-logo" class="tab-panel<?= $activeTab === 'logo' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-logo.php'; ?></div>
      </section>

      <section id="tab-faq" class="tab-panel<?= $activeTab === 'faq' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-faq.php'; ?></div>
      </section>

      <section id="tab-password" class="tab-panel tab-archive<?= $activeTab === 'password' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-password.php'; ?></div>
      </section>

      <section id="tab-layanan" class="tab-panel tab-archive<?= $activeTab === 'layanan' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-layanan.php'; ?></div>
      </section>

      <section id="tab-statistik" class="tab-panel tab-archive<?= $activeTab === 'statistik' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-statistik.php'; ?></div>
      </section>

      <section id="tab-gambar" class="tab-panel tab-archive<?= $activeTab === 'gambar' ? ' tab-active' : '' ?>">
        <div><?php include __DIR__ . '/views/tab-gambar.php'; ?></div>
      </section>
    </div>

    <script>
    // Enhancement saja: pindah tab tanpa reload. Tanpa JS, link ?tab= tetap jalan (full reload via PHP).
    (function() {
      var links = document.querySelectorAll('.tab-link[data-tab]');
      var panels = document.querySelectorAll('.tab-panel');
      function activate(target, syncUrl) {
        var found = false;
        panels.forEach(function(p) {
          var on = (p.id === ('tab-' + target));
          if (on) { found = true; }
          p.classList.toggle('tab-active', on);
        });
        if (!found) { return; }
        links.forEach(function(l) {
          l.classList.toggle('tab-active', l.getAttribute('data-tab') === target);
        });
        if (syncUrl) {
          try { history.replaceState(null, '', 'admin.php?tab=' + target); } catch (e) {}
        }
      }
      links.forEach(function(link) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          activate(this.getAttribute('data-tab'), true);
        });
      });
    })();

    var faqList = document.getElementById("faq-items");
    if (faqList) {
      document.getElementById("add-faq").addEventListener("click", function() {
        if (faqList.children.length >= 20) return;
        var item = document.createElement("div");
        item.className = "faq-item";
        var questionLabel = document.createElement("label");
        questionLabel.textContent = "Pertanyaan";
        var question = document.createElement("input");
        question.name = "faq_questions[]";
        question.maxLength = 160;
        question.required = true;
        questionLabel.appendChild(question);
        var answerLabel = document.createElement("label");
        answerLabel.textContent = "Jawaban";
        var answer = document.createElement("textarea");
        answer.name = "faq_answers[]";
        answer.maxLength = 2000;
        answer.required = true;
        answerLabel.appendChild(answer);
        var remove = document.createElement("button");
        remove.className = "remove-faq";
        remove.type = "button";
        remove.textContent = "Hapus";
        item.append(questionLabel, answerLabel, remove);
        faqList.appendChild(item);
        question.focus();
      });
      faqList.addEventListener("click", function(event) {
        if (event.target.matches(".remove-faq")) {
          event.target.closest(".faq-item").remove();
        }
      });
    }
    </script>
  <?php endif; ?>
</main>
<footer class="wrap">CMS Pojok Berkah</footer>
</body>
</html>
