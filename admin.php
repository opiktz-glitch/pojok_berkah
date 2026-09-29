<?php

declare(strict_types=1);

$storagePath = __DIR__ . '/data/storage.php';
define('POJOK_BERKAH_BOOTSTRAPPED', true);
$settings = is_file($storagePath) ? require $storagePath : [];
if (!is_array($settings)) {
    $settings = ['wa_number' => '6287724039666', 'password_hash' => ''];
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

function saveSettings(string $path, array $settings): bool
{
  $content = "<?php\nif (!defined('POJOK_BERKAH_BOOTSTRAPPED')) { http_response_code(404); exit; }\nreturn " . var_export($settings, true) . ";\n";
    return file_put_contents($path, $content, LOCK_EX) !== false;
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isValidAdminPassword(string $password): bool
{
  return strlen($password) >= 6
    && strlen($password) <= 72
    && preg_match('/[A-Za-z]/', $password) === 1
    && preg_match('/[0-9]/', $password) === 1;
}

$defaultFaq = [
  ['question' => 'Apakah semua layanan memakai satu akun?', 'answer' => 'Tidak. Tiap layanan berjalan di alamatnya sendiri dan punya aturannya sendiri. Halaman ini hanya pintu masuknya.'],
  ['question' => 'Apakah hasil simulasi gadai sama dengan pencairan final?', 'answer' => 'Belum tentu. Simulasi adalah perkiraan. Nilai final ditentukan setelah verifikasi dan survei oleh lembaga pembiayaan.'],
  ['question' => 'Siapa yang bisa memakai Tryout TKA?', 'answer' => 'Siswa yang ingin berlatih soal, serta guru atau sekolah yang ingin mengelola soal dan paket tryout.'],
  ['question' => 'Bagaimana cara memesan di Alfi Kitchen?', 'answer' => 'Buka halaman Alfi Kitchen, pilih produk, lalu pesan lewat tombol WhatsApp.'],
];
$faqItems = [];
$storedFaq = $settings['faq'] ?? $defaultFaq;
if (is_array($storedFaq)) {
  foreach ($storedFaq as $item) {
    if (!is_array($item)) {
      continue;
    }
    $question = trim((string) ($item['question'] ?? ''));
    $answer = trim((string) ($item['answer'] ?? ''));
    if ($question !== '' && $answer !== '') {
      $faqItems[] = ['question' => $question, 'answer' => $answer];
    }
  }
}
if ($faqItems === []) {
  $faqItems = $defaultFaq;
}

$imageSlots = [
  'logo' => 'Logo situs',
  'tryout' => 'Tryout TKA',
  'gadai' => 'Gadai BPKB',
  'kitchen' => 'Alfi Kitchen',
];
$defaultImageUrls = [
  'logo' => '',
  'tryout' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=960&q=80',
  'gadai' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=960&q=80',
  'kitchen' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=960&q=80',
];
$defaultServiceUrls = [
  'tryout' => 'https://tryout.pojokberkah.online',
  'gadai' => 'https://gadai.pojokberkah.online',
  'kitchen' => 'https://kitchen.pojokberkah.online',
];
$serviceUrls = [];
foreach ($defaultServiceUrls as $slot => $defaultUrl) {
    $serviceUrls[$slot] = normalizeServiceUrl((string) ($settings['service_urls'][$slot] ?? '')) ?? $defaultUrl;
}
$defaultServiceNames = [
  'tryout' => 'Tryout TKA',
  'gadai' => 'Gadai BPKB',
  'kitchen' => 'Alfi Kitchen',
];
$serviceNames = [];
foreach ($defaultServiceNames as $slot => $defaultName) {
    $name = $settings['service_names'][$slot] ?? null;
    $serviceNames[$slot] = is_string($name) && trim($name) !== '' && strlen($name) <= 400
        ? trim($name)
        : $defaultName;
}
$imageSlots['tryout'] = $serviceNames['tryout'];
$imageSlots['gadai'] = $serviceNames['gadai'];
$imageSlots['kitchen'] = $serviceNames['kitchen'];
$defaultServiceDescriptions = [
  'tryout' => 'Latihan soal TKA online lengkap dengan pembahasan, disusun dengan bantuan AI untuk siswa dan guru.',
  'gadai' => 'Simulasi pencairan dana dengan jaminan BPKB, tersedia di tiga kota dengan 29 cabang aktif.',
  'kitchen' => 'Puding berlapis buah, dessert dalam kemasan praktis, dan salad buah bersaus creamy. Buatan rumahan, selalu segar.',
];
$serviceDescriptions = [];
foreach ($defaultServiceDescriptions as $slot => $defaultDescription) {
    $description = $settings['service_descriptions'][$slot] ?? null;
    $serviceDescriptions[$slot] = is_string($description) && trim($description) !== '' && strlen($description) <= 2000
        ? trim($description)
        : $defaultDescription;
}
$defaultStats = [
  'services' => ['value' => '3', 'label' => 'layanan'],
  'gadai' => ['value' => '29', 'label' => 'cabang gadai aktif'],
  'kitchen' => ['value' => 'Always Fresh', 'label' => 'komitmen Alfi Kitchen'],
];
$stats = [];
foreach ($defaultStats as $slot => $defaults) {
  $stored = $settings['stats'][$slot] ?? [];
  $value = is_array($stored) ? ($stored['value'] ?? null) : null;
  $label = is_array($stored) ? ($stored['label'] ?? null) : null;
  $stats[$slot] = [
    'value' => is_string($value) && trim($value) !== '' && strlen($value) <= 320 ? trim($value) : $defaults['value'],
    'label' => is_string($label) && trim($label) !== '' && strlen($label) <= 480 ? trim($label) : $defaults['label'],
  ];
}
$defaultServiceTags = [
  'tryout' => ['Soal AI', 'Pembahasan', 'Siswa dan guru'],
  'gadai' => ['Bandung', 'Bekasi', 'Jakarta'],
  'kitchen' => ['Puding', 'Dessert', 'Salad buah'],
];
$storedServiceTags = is_array($settings['service_tags'] ?? null) ? $settings['service_tags'] : [];
$serviceTags = [];
foreach ($defaultServiceTags as $slot => $defaultTags) {
  $storedTags = $storedServiceTags[$slot] ?? null;
  if (!is_array($storedTags)) {
    $serviceTags[$slot] = $defaultTags;
    continue;
  }
  $serviceTags[$slot] = [];
  foreach ($storedTags as $tag) {
    if (is_string($tag) && trim($tag) !== '' && strlen($tag) <= 240 && count($serviceTags[$slot]) < 8) {
      $serviceTags[$slot][] = trim($tag);
    }
  }
}
$uploadDirectory = __DIR__ . '/data/uploads';

if (isset($_GET['image'])) {
  $slot = (string) $_GET['image'];
  $filename = (string) ($settings['images'][$slot] ?? '');
  if (!isset($imageSlots[$slot]) || !preg_match('/\A[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $filename)) {
    http_response_code(404);
    exit;
  }
  $imagePath = $uploadDirectory . '/' . $filename;
  if (!is_file($imagePath)) {
    http_response_code(404);
    exit;
  }
  $mimeByExtension = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
  header('X-Content-Type-Options: nosniff');
  header('X-Robots-Tag: noindex, nofollow');
  header('Cache-Control: public, max-age=86400');
  header('Content-Type: ' . $mimeByExtension[pathinfo($filename, PATHINFO_EXTENSION)]);
  header('Content-Length: ' . filesize($imagePath));
  readfile($imagePath);
  exit;
}

if (isset($_GET['public'])) {
  header('X-Robots-Tag: noindex, nofollow');
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, max-age=0');
    $publicNumber = normalizeWhatsAppNumber((string) ($settings['wa_number'] ?? ''));
  $publicImages = $defaultImageUrls;
  foreach ($imageSlots as $slot => $label) {
    $filename = (string) ($settings['images'][$slot] ?? '');
    if (preg_match('/\A[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $filename) && is_file($uploadDirectory . '/' . $filename)) {
      $publicImages[$slot] = 'admin.php?image=' . $slot . '&v=' . substr($filename, 0, 12);
    }
  }
  echo json_encode(['wa_number' => $publicNumber ?? '6287724039666', 'faq' => $faqItems, 'images' => $publicImages, 'service_urls' => $serviceUrls, 'service_descriptions' => $serviceDescriptions, 'service_names' => $serviceNames, 'stats' => $stats, 'service_tags' => $serviceTags], JSON_UNESCAPED_SLASHES);
    exit;
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('X-Robots-Tag: noindex, nofollow, noarchive');
$usingHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => $usingHttps,
    'samesite' => 'Strict',
]);
session_start();

if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = (string) $_SESSION['csrf'];
$flash = '';
$setupRequired = trim((string) ($settings['password_hash'] ?? '')) === '';
$remoteAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
$canSetup = true;
$isAuthenticated = !empty($_SESSION['authenticated']);
$loginAttempts = is_array($settings['login_attempts'] ?? null) ? $settings['login_attempts'] : [];
$loginNow = time();
$loginWindow = 900;
foreach ($loginAttempts as $attemptKey => $attempt) {
  if (!is_array($attempt) || ((int) ($attempt['locked_until'] ?? 0) <= $loginNow && (int) ($attempt['window_start'] ?? 0) < $loginNow - $loginWindow)) {
    unset($loginAttempts[$attemptKey]);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedCsrf = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $postedCsrf)) {
        $flash = 'Sesi keamanan tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'setup' && $setupRequired && $canSetup) {
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['password_confirmation'] ?? '');
            if (!isValidAdminPassword($password)) {
              $flash = 'Password admin minimal 6 karakter dan harus mengandung huruf serta angka.';
            } elseif (!hash_equals($password, $confirmation)) {
                $flash = 'Konfirmasi password belum sama.';
            } else {
                $settings['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                if (saveSettings($storagePath, $settings)) {
                    session_regenerate_id(true);
                    $_SESSION['authenticated'] = true;
                    $_SESSION['csrf'] = bin2hex(random_bytes(32));
                    $csrf = (string) $_SESSION['csrf'];
                    $setupRequired = false;
                    $isAuthenticated = true;
                    $flash = 'Akun admin berhasil dibuat.';
                } else {
                    $flash = 'Pengaturan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
            }
        } elseif ($action === 'login' && !$setupRequired && !$isAuthenticated) {
            $password = (string) ($_POST['password'] ?? '');
          $attemptKey = hash_hmac('sha256', $remoteAddress !== '' ? $remoteAddress : 'unknown', (string) $settings['password_hash']);
          $attempt = $loginAttempts[$attemptKey] ?? ['count' => 0, 'window_start' => $loginNow, 'locked_until' => 0];
          $lockedUntil = (int) ($attempt['locked_until'] ?? 0);
          if ($lockedUntil > $loginNow) {
            $minutes = (int) ceil(($lockedUntil - $loginNow) / 60);
            $flash = 'Terlalu banyak percobaan login. Coba lagi dalam sekitar ' . $minutes . ' menit.';
          } elseif (password_verify($password, (string) $settings['password_hash'])) {
            unset($loginAttempts[$attemptKey]);
            if ($loginAttempts === []) {
              unset($settings['login_attempts']);
            } else {
              $settings['login_attempts'] = $loginAttempts;
            }
            saveSettings($storagePath, $settings);
                session_regenerate_id(true);
                $_SESSION['authenticated'] = true;
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                $csrf = (string) $_SESSION['csrf'];
                $isAuthenticated = true;
                $flash = 'Berhasil masuk.';
            } else {
            if ((int) ($attempt['window_start'] ?? 0) < $loginNow - $loginWindow) {
              $attempt = ['count' => 0, 'window_start' => $loginNow, 'locked_until' => 0];
            }
            $attempt['count'] = (int) ($attempt['count'] ?? 0) + 1;
            if ($attempt['count'] >= 5) {
              $attempt['locked_until'] = $loginNow + $loginWindow;
              $flash = 'Terlalu banyak percobaan login. Akses ditahan selama 15 menit.';
            } else {
              $attempt['locked_until'] = 0;
              $flash = 'Password tidak sesuai. Sisa percobaan sebelum jeda: ' . (5 - $attempt['count']) . '.';
            }
            $loginAttempts[$attemptKey] = $attempt;
            $settings['login_attempts'] = $loginAttempts;
            if (!saveSettings($storagePath, $settings)) {
              $flash = 'Percobaan gagal dan pembatas login tidak dapat disimpan. Periksa izin tulis data CMS.';
            }
            }
        } elseif ($action === 'save_service_urls' && $isAuthenticated) {
          $submittedUrls = $_POST['service_urls'] ?? [];
          $normalizedUrls = [];
          $urlError = '';
          if (!is_array($submittedUrls)) {
            $urlError = 'Format URL layanan tidak valid.';
          } else {
            foreach ($defaultServiceUrls as $slot => $defaultUrl) {
              $urlInput = $submittedUrls[$slot] ?? null;
              $normalized = is_string($urlInput) ? normalizeServiceUrl($urlInput) : null;
              if ($normalized === null) {
                $urlError = 'Masukkan URL penuh yang valid dengan awalan http:// atau https:// untuk setiap layanan.';
                break;
              }
              $normalizedUrls[$slot] = $normalized;
            }
          }
          if ($urlError !== '') {
            $flash = $urlError;
            if (is_array($submittedUrls)) {
              foreach ($defaultServiceUrls as $serviceSlot => $defaultUrl) {
                if (isset($submittedUrls[$serviceSlot]) && is_string($submittedUrls[$serviceSlot])) {
                  $serviceUrls[$serviceSlot] = $submittedUrls[$serviceSlot];
                }
              }
            }
          } else {
            $settings['service_urls'] = $normalizedUrls;
            if (saveSettings($storagePath, $settings)) {
              $serviceUrls = $normalizedUrls;
              $flash = 'Alamat layanan berhasil diperbarui.';
            } else {
              $flash = 'Alamat layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
            }
          }
            } elseif ($action === 'save_service_names' && $isAuthenticated) {
              $submittedNames = $_POST['service_names'] ?? [];
              $normalizedNames = [];
              $nameError = '';
              if (!is_array($submittedNames)) {
                $nameError = 'Format nama layanan tidak valid.';
              } else {
                foreach ($defaultServiceNames as $slot => $defaultName) {
                  $name = $submittedNames[$slot] ?? null;
                  if (!is_string($name)) {
                    $nameError = 'Isi nama untuk setiap layanan.';
                    break;
                  }
                  $name = trim($name);
                  if ($name === '' || strlen($name) > 400) {
                    $nameError = 'Nama layanan wajib diisi dan maksimal 100 karakter.';
                    break;
                  }
                  $normalizedNames[$slot] = $name;
                }
              }
              if ($nameError !== '') {
                $flash = $nameError;
                if (is_array($submittedNames)) {
                  foreach ($defaultServiceNames as $slot => $defaultName) {
                    if (isset($submittedNames[$slot]) && is_string($submittedNames[$slot])) {
                      $serviceNames[$slot] = trim($submittedNames[$slot]);
                    }
                  }
                }
              } else {
                $settings['service_names'] = $normalizedNames;
                if (saveSettings($storagePath, $settings)) {
                  $serviceNames = $normalizedNames;
                  $imageSlots['tryout'] = $serviceNames['tryout'];
                  $imageSlots['gadai'] = $serviceNames['gadai'];
                  $imageSlots['kitchen'] = $serviceNames['kitchen'];
                  $flash = 'Nama layanan berhasil diperbarui.';
                } else {
                  $flash = 'Nama layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
              }
            } elseif ($action === 'save_service_tags' && $isAuthenticated) {
              $submittedTags = $_POST['service_tags'] ?? [];
              $normalizedTags = [];
              $tagsError = '';
              if (!is_array($submittedTags)) {
                $tagsError = 'Format tag layanan tidak valid.';
              } else {
                foreach ($defaultServiceTags as $slot => $defaults) {
                  $tagText = $submittedTags[$slot] ?? null;
                  if (!is_string($tagText) || strlen($tagText) > 2000) {
                    $tagsError = 'Periksa kembali daftar tag layanan.';
                    break;
                  }
                  $lines = preg_split('/\r\n|\r|\n/', $tagText);
                  if (!is_array($lines)) {
                    $tagsError = 'Daftar tag tidak dapat dibaca.';
                    break;
                  }
                  $tags = [];
                  foreach ($lines as $line) {
                    $tag = trim($line);
                    if ($tag === '') {
                      continue;
                    }
                    if (strlen($tag) > 240 || count($tags) >= 8) {
                      $tagsError = 'Setiap layanan maksimal 8 tag, masing-masing 60 karakter.';
                      break 2;
                    }
                    $tags[] = $tag;
                  }
                  $normalizedTags[$slot] = $tags;
                }
              }
              if ($tagsError !== '') {
                $flash = $tagsError;
                if (is_array($submittedTags)) {
                  foreach ($defaultServiceTags as $slot => $defaults) {
                    if (isset($submittedTags[$slot]) && is_string($submittedTags[$slot])) {
                      $lines = preg_split('/\r\n|\r|\n/', $submittedTags[$slot]) ?: [];
                      $serviceTags[$slot] = array_values(array_filter(array_map('trim', $lines), 'strlen'));
                    }
                  }
                }
              } else {
                $settings['service_tags'] = $normalizedTags;
                if (saveSettings($storagePath, $settings)) {
                  $serviceTags = $normalizedTags;
                  $flash = 'Tag layanan berhasil diperbarui.';
                } else {
                  $flash = 'Tag layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
              }
            } elseif ($action === 'save_stats' && $isAuthenticated) {
              $submittedStats = $_POST['stats'] ?? [];
              $normalizedStats = [];
              $statsError = '';
              if (!is_array($submittedStats)) {
                $statsError = 'Format statistik tidak valid.';
              } else {
                foreach ($defaultStats as $slot => $defaults) {
                  $entry = $submittedStats[$slot] ?? null;
                  if (!is_array($entry) || !is_string($entry['value'] ?? null) || !is_string($entry['label'] ?? null)) {
                    $statsError = 'Isi nilai dan label untuk setiap statistik.';
                    break;
                  }
                  $value = trim($entry['value']);
                  $label = trim($entry['label']);
                  if ($value === '' || strlen($value) > 320 || $label === '' || strlen($label) > 480) {
                    $statsError = 'Nilai dan label statistik wajib diisi dan tidak boleh terlalu panjang.';
                    break;
                  }
                  $normalizedStats[$slot] = ['value' => $value, 'label' => $label];
                }
              }
              if ($statsError !== '') {
                $flash = $statsError;
              } else {
                $settings['stats'] = $normalizedStats;
                if (saveSettings($storagePath, $settings)) {
                  $stats = $normalizedStats;
                  $flash = 'Statistik beranda berhasil diperbarui.';
                } else {
                  $flash = 'Statistik tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
              }
            } elseif ($action === 'save_service_descriptions' && $isAuthenticated) {
              $submittedDescriptions = $_POST['service_descriptions'] ?? [];
              $normalizedDescriptions = [];
              $descriptionError = '';
              if (!is_array($submittedDescriptions)) {
                $descriptionError = 'Format deskripsi layanan tidak valid.';
              } else {
                foreach ($defaultServiceDescriptions as $slot => $defaultDescription) {
                  $description = $submittedDescriptions[$slot] ?? null;
                  if (!is_string($description)) {
                    $descriptionError = 'Isi deskripsi untuk setiap layanan.';
                    break;
                  }
                  $description = trim($description);
                  if ($description === '' || strlen($description) > 2000) {
                    $descriptionError = 'Deskripsi wajib diisi dan maksimal 500 karakter.';
                    break;
                  }
                  $normalizedDescriptions[$slot] = $description;
                }
              }
              if ($descriptionError !== '') {
                $flash = $descriptionError;
                if (is_array($submittedDescriptions)) {
                  foreach ($defaultServiceDescriptions as $slot => $defaultDescription) {
                    if (isset($submittedDescriptions[$slot]) && is_string($submittedDescriptions[$slot])) {
                      $serviceDescriptions[$slot] = trim($submittedDescriptions[$slot]);
                    }
                  }
                }
              } else {
                $settings['service_descriptions'] = $normalizedDescriptions;
                if (saveSettings($storagePath, $settings)) {
                  $serviceDescriptions = $normalizedDescriptions;
                  $flash = 'Deskripsi layanan berhasil diperbarui.';
                } else {
                  $flash = 'Deskripsi layanan tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
              }
        } elseif ($action === 'upload_image' && $isAuthenticated) {
          $slot = (string) ($_POST['image_slot'] ?? '');
          $file = $_FILES['service_image'] ?? null;
          $imageTypes = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
          if (!isset($imageSlots[$slot]) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $flash = 'Pilih gambar layanan yang valid untuk diunggah.';
          } elseif (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            $flash = 'Ukuran gambar maksimal 5 MB.';
          } else {
            $imageInfo = @getimagesize((string) $file['tmp_name']);
            $extension = is_array($imageInfo) ? ($imageTypes[$imageInfo[2]] ?? null) : null;
            if ($extension === null) {
              $flash = 'Format gambar harus JPEG, PNG, atau WebP. SVG tidak didukung.';
            } elseif (($imageInfo[0] ?? 0) > 12000 || ($imageInfo[1] ?? 0) > 12000) {
              $flash = 'Dimensi gambar maksimal 12.000 piksel per sisi.';
            } else {
              if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                $flash = 'Folder gambar tidak dapat dibuat. Periksa izin tulis di hosting.';
              } elseif (!is_writable($uploadDirectory)) {
                $flash = 'Folder gambar tidak memiliki izin tulis di hosting.';
              } else {
                $filename = bin2hex(random_bytes(16)) . '.' . $extension;
                $newImagePath = $uploadDirectory . '/' . $filename;
                if (!move_uploaded_file((string) $file['tmp_name'], $newImagePath)) {
                  $flash = 'Gambar gagal dipindahkan ke penyimpanan hosting.';
                } else {
                  $settings['images'] = is_array($settings['images'] ?? null) ? $settings['images'] : [];
                  $oldFilename = (string) ($settings['images'][$slot] ?? '');
                  $settings['images'][$slot] = $filename;
                  if (saveSettings($storagePath, $settings)) {
                    if (preg_match('/\A[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $oldFilename) && !in_array($oldFilename, $settings['images'], true)) {
                      $oldImagePath = $uploadDirectory . '/' . $oldFilename;
                      if (is_file($oldImagePath)) {
                        unlink($oldImagePath);
                      }
                    }
                    $flash = 'Gambar ' . $imageSlots[$slot] . ' berhasil diperbarui.';
                  } else {
                    unset($settings['images'][$slot]);
                    if ($oldFilename !== '') {
                      $settings['images'][$slot] = $oldFilename;
                    }
                    if (is_file($newImagePath)) {
                      unlink($newImagePath);
                    }
                    $flash = 'Gambar tidak dapat disimpan. Pastikan file pengaturan dapat ditulis oleh PHP.';
                  }
                }
              }
            }
          }
        } elseif ($action === 'remove_image' && $isAuthenticated) {
          $slot = (string) ($_POST['image_slot'] ?? '');
          if (!isset($imageSlots[$slot])) {
            $flash = 'Kategori gambar tidak valid.';
          } else {
            $filename = (string) ($settings['images'][$slot] ?? '');
            unset($settings['images'][$slot]);
            if (saveSettings($storagePath, $settings)) {
              if (preg_match('/\A[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $filename) && !in_array($filename, $settings['images'] ?? [], true)) {
                $oldImagePath = $uploadDirectory . '/' . $filename;
                if (is_file($oldImagePath)) {
                  unlink($oldImagePath);
                }
              }
              $flash = 'Gambar ' . $imageSlots[$slot] . ' dikembalikan ke gambar bawaan.';
            } else {
              $settings['images'][$slot] = $filename;
              $flash = 'Pengaturan gambar tidak dapat disimpan.';
            }
          }
        } elseif ($action === 'save' && $isAuthenticated) {
            $number = normalizeWhatsAppNumber((string) ($_POST['wa_number'] ?? ''));
            if ($number === null) {
                $flash = 'Masukkan nomor yang valid, 8 sampai 15 digit termasuk kode negara.';
            } else {
                $settings['wa_number'] = $number;
                if (saveSettings($storagePath, $settings)) {
                    $flash = 'Nomor WhatsApp berhasil diperbarui.';
                } else {
                    $flash = 'Nomor tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                }
            }
              } elseif ($action === 'save_faq' && $isAuthenticated) {
                $questions = $_POST['faq_questions'] ?? [];
                $answers = $_POST['faq_answers'] ?? [];
                $submittedFaq = [];
                $faqError = '';
                if (!is_array($questions) || !is_array($answers) || count($questions) !== count($answers) || count($questions) > 20) {
                  $faqError = 'Periksa kembali daftar pertanyaan. Maksimal 20 item.';
                } else {
                  foreach ($questions as $index => $questionValue) {
                    $question = trim((string) $questionValue);
                    $answer = trim((string) ($answers[$index] ?? ''));
                    if ($question === '' && $answer === '') {
                      continue;
                    }
                    $submittedFaq[] = ['question' => $question, 'answer' => $answer];
                    if ($question === '' || $answer === '') {
                      $faqError = 'Isi pertanyaan dan jawaban untuk setiap item.';
                      break;
                    }
                    if (strlen($question) > 320 || strlen($answer) > 4000) {
                      $faqError = 'Pertanyaan atau jawaban terlalu panjang.';
                      break;
                    }
                  }
                  if ($faqError === '' && $submittedFaq === []) {
                    $faqError = 'Tambahkan minimal satu pertanyaan dan jawaban.';
                  }
                }
                if ($faqError !== '') {
                  $flash = $faqError;
                  $faqItems = $submittedFaq !== [] ? $submittedFaq : $faqItems;
                } else {
                  $settings['faq'] = $submittedFaq;
                  if (saveSettings($storagePath, $settings)) {
                    $faqItems = $submittedFaq;
                    $flash = 'Pertanyaan dan jawaban berhasil diperbarui.';
                  } else {
                    $faqItems = $submittedFaq;
                    $flash = 'FAQ tidak dapat disimpan. Pastikan folder data bisa ditulis oleh PHP.';
                  }
                }
        } elseif ($action === 'change_password' && $isAuthenticated) {
          $currentPassword = (string) ($_POST['current_password'] ?? '');
          $newPassword = (string) ($_POST['new_password'] ?? '');
          $confirmation = (string) ($_POST['new_password_confirmation'] ?? '');
          if (!password_verify($currentPassword, (string) $settings['password_hash'])) {
            $flash = 'Password saat ini tidak sesuai.';
          } elseif (!isValidAdminPassword($newPassword)) {
            $flash = 'Password baru harus 6 sampai 72 karakter dan mengandung huruf serta angka.';
          } elseif (!hash_equals($newPassword, $confirmation)) {
            $flash = 'Konfirmasi password baru belum sama.';
          } elseif (password_verify($newPassword, (string) $settings['password_hash'])) {
            $flash = 'Password baru harus berbeda dari password saat ini.';
          } else {
            $previousPasswordHash = $settings['password_hash'];
            $settings['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            unset($settings['login_attempts']);
            if (saveSettings($storagePath, $settings)) {
              session_regenerate_id(true);
              $_SESSION['authenticated'] = true;
              $_SESSION['csrf'] = bin2hex(random_bytes(32));
              $csrf = (string) $_SESSION['csrf'];
              $flash = 'Password admin berhasil diganti.';
            } else {
              $settings['password_hash'] = $previousPasswordHash;
              $flash = 'Password tidak dapat disimpan. Periksa izin tulis file pengaturan.';
            }
          }
        } elseif ($action === 'logout' && $isAuthenticated) {
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            $csrf = (string) $_SESSION['csrf'];
            $isAuthenticated = false;
            $flash = 'Anda sudah keluar.';
        }
    }
}

$waNumber = normalizeWhatsAppNumber((string) ($settings['wa_number'] ?? '6287724039666')) ?? '6287724039666';
$imageSources = $defaultImageUrls;
foreach ($imageSlots as $slot => $label) {
  $filename = (string) ($settings['images'][$slot] ?? '');
  if (preg_match('/\A[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $filename) && is_file($uploadDirectory . '/' . $filename)) {
    $imageSources[$slot] = 'admin.php?image=' . $slot . '&v=' . substr($filename, 0, 12);
  }
}
$csrf = (string) ($_SESSION['csrf'] ?? $csrf);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#F4F8FC">
<meta name="robots" content="noindex,nofollow,noarchive">
<title>CMS Situs | Pojok Berkah</title>
<link rel="icon" href="<?= $imageSources['logo'] !== '' ? escapeHtml($imageSources['logo']) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22%3E%3Crect width=%2232%22 height=%2232%22 rx=%229%22 fill=%22%230B8277%22/%3E%3Ctext x=%2216%22 y=%2223%22 font-size=%2220%22 font-family=%22Arial%22 font-weight=%22700%22 text-anchor=%22middle%22 fill=%22%23fff%22%3EP%3C/text%3E%3C/svg%3E' ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#F4F8FC;--ink:#10203A;--muted:#51607A;--line:rgba(16,32,58,.12);--teal:#0B8277;--blue:#2F5BEA;--green:#25D366;--display:"Sora","Trebuchet MS",system-ui,sans-serif;--body:"Inter",system-ui,-apple-system,"Segoe UI",sans-serif}
*{box-sizing:border-box;margin:0}
body{min-height:100vh;font-family:var(--body);color:var(--ink);background:radial-gradient(700px 400px at 8% 4%,rgba(62,201,187,.2),transparent 70%),radial-gradient(700px 420px at 95% 10%,rgba(106,147,255,.18),transparent 70%),var(--bg);line-height:1.6}
a{color:inherit}
.wrap{width:min(100% - 36px,1040px);margin:0 auto}
header{height:72px;border-bottom:1px solid var(--line);background:rgba(244,248,252,.84);backdrop-filter:blur(12px)}
.top{height:100%;display:flex;align-items:center;justify-content:space-between;gap:16px}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;font-family:var(--display);font-weight:700}
.brand-image{width:34px;height:34px;flex:0 0 34px;object-fit:contain}
.brand-image[hidden]{display:none}
.mark{width:34px;height:34px;display:grid;place-items:center;border-radius:9px;color:#fff;background:linear-gradient(135deg,var(--teal),var(--blue))}
.top nav{display:flex;align-items:center;gap:22px;font-size:.92rem}
.top nav a{text-decoration:none;color:var(--muted)}
main{padding:72px 0}
.kicker{color:var(--teal);font-weight:700;font-size:.8rem;text-transform:uppercase}
h1{font-family:var(--display);font-size:2.6rem;line-height:1.15;margin-top:10px}
.lead{color:var(--muted);margin-top:12px;max-width:62ch}
.panel{margin-top:34px;display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:36px;align-items:start;padding:32px;border:1px solid var(--line);border-radius:8px;background:rgba(255,255,255,.78);box-shadow:0 18px 50px -36px rgba(16,32,58,.4)}
.faq-panel{grid-template-columns:minmax(0,1fr);margin-top:22px}
.service-content-panel{display:block;margin-top:22px}
.service-content-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px 36px;margin-top:20px}
.service-content-group{min-width:0;padding-top:16px;border-top:1px solid var(--line)}
.service-content-group h3{font:600 1rem var(--display)}
.service-content-group .form-copy{min-height:3em}
.faq-item{display:grid;grid-template-columns:1fr 1.3fr auto;align-items:end;gap:14px;padding:18px 0;border-bottom:1px solid var(--line)}
.images-panel{display:block;margin-top:22px}
.image-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:22px}
.image-card{min-width:0;padding:15px;border:1px solid var(--line);border-radius:6px;background:rgba(255,255,255,.72)}
.image-card img{display:block;width:100%;height:148px;object-fit:cover;border-radius:4px;background:#e5edf2}
.image-card img.image-logo-preview{object-fit:contain;padding:12px;background:#fff}
.image-placeholder{height:148px;display:grid;place-items:center;border-radius:4px;background:#eef4f5}
.image-placeholder .mark{width:54px;height:54px;font-size:1.3rem}
.image-card label{margin-top:14px}
.image-status{margin-top:8px;color:var(--muted);font-size:.82rem}
.image-card input[type=file]{height:auto;min-height:44px;padding:8px;font-size:.84rem}
.image-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.image-actions button{padding:0 12px;font-size:.82rem}
.image-reset{color:#8f2929;background:#fceeee}
.faq-item label{margin-top:0}
textarea{display:block;width:100%;min-height:96px;margin-top:8px;padding:12px 14px;border:1px solid rgba(16,32,58,.2);border-radius:5px;font:inherit;color:var(--ink);background:#fff;resize:vertical}
textarea:focus{outline:3px solid rgba(47,91,234,.2);border-color:var(--blue)}
.remove-faq{min-height:42px;margin-bottom:4px;color:#8f2929;background:#fceeee}
h2{font-family:var(--display);font-size:1.2rem}
.form-copy{color:var(--muted);font-size:.94rem;margin-top:7px}
.password-form{margin-top:28px;padding-top:24px;border-top:1px solid var(--line)}
label{display:block;font-size:.9rem;font-weight:600;margin-top:23px}
input{display:block;width:100%;height:50px;margin-top:8px;padding:0 14px;border:1px solid rgba(16,32,58,.2);border-radius:5px;font:inherit;color:var(--ink);background:#fff}
input:focus{outline:3px solid rgba(47,91,234,.2);border-color:var(--blue)}
.hint{font-size:.83rem;color:var(--muted);margin-top:7px}
.actions{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:22px}
button,.button{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border:0;border-radius:5px;font:600 .94rem var(--body);text-decoration:none;cursor:pointer}
.primary{color:#fff;background:linear-gradient(120deg,var(--teal),var(--blue))}
.primary:hover{filter:brightness(1.06)}
.secondary{color:var(--ink);background:#edf2f7}
.flash{margin-top:20px;padding:12px 14px;border-left:3px solid var(--teal);background:#e9f7f4;font-size:.9rem}
.preview{padding:20px;background:#eef4f5;border-radius:6px}
.preview span{font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase}
.preview strong{display:block;margin-top:7px;font:600 1.1rem var(--display);overflow-wrap:anywhere}
.preview a{display:flex;margin-top:16px;min-height:44px;align-items:center;justify-content:center;border-radius:5px;background:var(--green);color:#04361A;font-weight:700;text-decoration:none}
.notice{margin-top:16px;color:var(--muted);font-size:.82rem}
footer{padding:0 0 34px;color:var(--muted);font-size:.86rem}
@media(max-width:700px){main{padding:48px 0}.panel{grid-template-columns:1fr;padding:22px;gap:24px}.top nav{gap:12px}.top nav a:first-child{display:none}h1{font-size:2.1rem}}
@media(max-width:700px){.service-content-grid{grid-template-columns:1fr;gap:18px}}
@media(max-width:700px){.faq-item{grid-template-columns:1fr}.remove-faq{justify-self:start}}
@media(max-width:700px){.image-grid{grid-template-columns:1fr 1fr}}
@media(max-width:460px){.image-grid{grid-template-columns:1fr}.image-card img{height:180px}}
@media(max-width:420px){.wrap{width:min(100% - 28px,1040px)}.top nav{font-size:.84rem}.panel{padding:18px}}
</style>
</head>
<body>
<header>
  <div class="wrap top">
    <a class="brand" href="index.html"><?php if (str_starts_with($imageSources['logo'], 'admin.php?image=')): ?><img class="brand-image" src="<?= escapeHtml($imageSources['logo']) ?>" alt=""><?php else: ?><span class="mark">P</span><?php endif; ?>Pojok Berkah <span style="font-family:var(--body);font-size:.78rem;font-weight:500;color:var(--muted)">/ CMS</span></a>
    <nav aria-label="Navigasi"><a href="index.html">Lihat situs</a><a href="tentang-kami.html">Tentang kami</a></nav>
  </div>
</header>
<main class="wrap">
  <span class="kicker">Pengaturan situs</span>
  <h1>Pengaturan situs</h1>
  <p class="lead">Kelola nomor WhatsApp admin, nama, alamat, deskripsi, gambar, statistik, dan pertanyaan layanan.</p>

  <?php if ($flash !== ''): ?>
    <p class="flash" role="status"><?= escapeHtml($flash) ?></p>
  <?php endif; ?>

  <section class="panel">
    <div>
      <?php if ($setupRequired && $canSetup): ?>
        <h2>Buat akun admin</h2>
        <p class="form-copy">Pilih password minimal 6 karakter dengan kombinasi huruf dan angka. Setup awal hanya tersedia dari komputer server (localhost).</p>
        <form method="post" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="setup">
          <label for="password">Password admin</label>
          <input id="password" name="password" type="password" minlength="6" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{6,}" title="Minimal 6 karakter, mengandung huruf dan angka." autocomplete="new-password" required>
          <label for="password_confirmation">Ulangi password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" minlength="6" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{6,}" title="Minimal 6 karakter, mengandung huruf dan angka." autocomplete="new-password" required>
          <div class="actions"><button class="primary" type="submit">Buat akun</button></div>
        </form>
        <p class="notice">Buat akun ini sebelum situs dipublikasikan. Password disimpan dalam bentuk hash, bukan teks biasa.</p>
      <?php elseif ($setupRequired): ?>
        <h2>Setup awal perlu dilakukan di server</h2>
        <p class="form-copy">Buka halaman ini dari komputer yang menjalankan PHP untuk membuat akun admin. Akses setup dari jaringan luar ditolak.</p>
      <?php elseif (!$isAuthenticated): ?>
        <h2>Masuk ke CMS</h2>
        <p class="form-copy">Masukkan password admin untuk melanjutkan.</p>
        <form method="post" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="login">
          <label for="password">Password admin</label>
          <input id="password" name="password" type="password" autocomplete="current-password" required autofocus>
          <div class="actions"><button class="primary" type="submit">Masuk</button></div>
        </form>
      <?php else: ?>
        <h2>Ubah nomor admin</h2>
        <p class="form-copy">Gunakan kode negara tanpa tanda plus. Format nomor Indonesia yang diawali 08 akan otomatis menjadi 62.</p>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="save">
          <label for="wa_number">Nomor WhatsApp</label>
          <input id="wa_number" name="wa_number" type="tel" inputmode="tel" value="<?= escapeHtml($waNumber) ?>" placeholder="6281234567890" required>
          <p class="hint">Contoh: 6281234567890 atau 081234567890</p>
          <div class="actions"><button class="primary" type="submit">Simpan nomor</button></div>
        </form>
        <form method="post" class="actions">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="logout">
          <button class="secondary" type="submit">Keluar</button>
        </form>
        <div class="password-form">
          <h2>Ganti password admin</h2>
          <p class="form-copy">Minimal 6 karakter dengan huruf dan angka. Untuk keamanan lebih baik, gunakan password unik yang panjang.</p>
          <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="action" value="change_password">
            <label for="current_password">Password saat ini</label>
            <input id="current_password" name="current_password" type="password" maxlength="72" autocomplete="current-password" required>
            <label for="new_password">Password baru</label>
            <input id="new_password" name="new_password" type="password" minlength="6" maxlength="72" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{6,72}" title="Minimal 6 karakter dan mengandung huruf serta angka." autocomplete="new-password" required>
            <label for="new_password_confirmation">Ulangi password baru</label>
            <input id="new_password_confirmation" name="new_password_confirmation" type="password" minlength="6" maxlength="72" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{6,72}" title="Minimal 6 karakter dan mengandung huruf serta angka." autocomplete="new-password" required>
            <div class="actions"><button class="primary" type="submit">Ganti password</button></div>
          </form>
        </div>
      <?php endif; ?>
    </div>
    <aside class="preview">
      <span>Nomor aktif</span>
      <strong>+<?= escapeHtml($waNumber) ?></strong>
      <a href="https://wa.me/<?= escapeHtml($waNumber) ?>" target="_blank" rel="noopener">Tes tautan WhatsApp</a>
      <p class="notice">Perubahan tersimpan di server dan digunakan oleh semua tombol WhatsApp pada situs.</p>
    </aside>
  </section>
  <?php if ($isAuthenticated): ?>
    <section class="panel service-content-panel">
      <h2>Konten layanan</h2>
      <p class="form-copy">Atur nama, tujuan, ringkasan, dan tag layanan dari satu bagian.</p>
      <div class="service-content-grid">
        <div class="service-content-group">
          <h3>Nama layanan</h3>
          <p class="form-copy">Nama tampil di kartu, footer, dan halaman Tentang Kami.</p>
          <form method="post">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="action" value="save_service_names">
            <label for="service-name-tryout">Tryout</label>
            <input id="service-name-tryout" name="service_names[tryout]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['tryout']) ?>" required>
            <label for="service-name-gadai">Gadai</label>
            <input id="service-name-gadai" name="service_names[gadai]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['gadai']) ?>" required>
            <label for="service-name-kitchen">Kitchen</label>
            <input id="service-name-kitchen" name="service_names[kitchen]" type="text" maxlength="100" value="<?= escapeHtml($serviceNames['kitchen']) ?>" required>
            <div class="actions"><button class="primary" type="submit">Simpan nama</button></div>
          </form>
        </div>
        <div class="service-content-group">
          <h3>Alamat layanan</h3>
          <p class="form-copy">Masukkan URL lengkap dengan awalan https://.</p>
          <form method="post">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="action" value="save_service_urls">
            <label for="service-url-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
            <input id="service-url-tryout" name="service_urls[tryout]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['tryout']) ?>" required>
            <label for="service-url-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
            <input id="service-url-gadai" name="service_urls[gadai]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['gadai']) ?>" required>
            <label for="service-url-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
            <input id="service-url-kitchen" name="service_urls[kitchen]" type="url" maxlength="2048" value="<?= escapeHtml($serviceUrls['kitchen']) ?>" required>
            <div class="actions"><button class="primary" type="submit">Simpan URL</button></div>
          </form>
        </div>
        <div class="service-content-group">
          <h3>Deskripsi</h3>
          <p class="form-copy">Ringkasan untuk kartu beranda dan halaman Tentang Kami.</p>
          <form method="post">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="action" value="save_service_descriptions">
            <label for="description-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
            <textarea id="description-tryout" name="service_descriptions[tryout]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['tryout']) ?></textarea>
            <label for="description-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
            <textarea id="description-gadai" name="service_descriptions[gadai]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['gadai']) ?></textarea>
            <label for="description-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
            <textarea id="description-kitchen" name="service_descriptions[kitchen]" maxlength="500" rows="3" required><?= escapeHtml($serviceDescriptions['kitchen']) ?></textarea>
            <div class="actions"><button class="primary" type="submit">Simpan deskripsi</button></div>
          </form>
        </div>
        <div class="service-content-group">
          <h3>Tag</h3>
          <p class="form-copy">Satu tag per baris; maksimal 8 tag dan 60 karakter per tag. Kosongkan untuk menyembunyikan chip.</p>
          <form method="post">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="action" value="save_service_tags">
            <label for="tags-tryout"><?= escapeHtml($serviceNames['tryout']) ?></label>
            <textarea id="tags-tryout" name="service_tags[tryout]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['tryout'])) ?></textarea>
            <label for="tags-gadai"><?= escapeHtml($serviceNames['gadai']) ?></label>
            <textarea id="tags-gadai" name="service_tags[gadai]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['gadai'])) ?></textarea>
            <label for="tags-kitchen"><?= escapeHtml($serviceNames['kitchen']) ?></label>
            <textarea id="tags-kitchen" name="service_tags[kitchen]" maxlength="2000" rows="3"><?= escapeHtml(implode("\n", $serviceTags['kitchen'])) ?></textarea>
            <div class="actions"><button class="primary" type="submit">Simpan tag</button></div>
          </form>
        </div>
      </div>
    </section>
  <?php endif; ?>
  <?php if ($isAuthenticated): ?>
    <section class="panel faq-panel">
      <div>
        <h2>Statistik beranda</h2>
        <p class="form-copy">Kelola nilai dan label yang tampil pada tiga statistik di beranda.</p>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="save_stats">
          <div class="faq-item">
            <label for="stat-services-value">Nilai jumlah layanan<input id="stat-services-value" name="stats[services][value]" maxlength="80" value="<?= escapeHtml($stats['services']['value']) ?>" required></label>
            <label for="stat-services-label">Label<input id="stat-services-label" name="stats[services][label]" maxlength="120" value="<?= escapeHtml($stats['services']['label']) ?>" required></label>
          </div>
          <div class="faq-item">
            <label for="stat-gadai-value">Nilai cabang gadai<input id="stat-gadai-value" name="stats[gadai][value]" maxlength="80" value="<?= escapeHtml($stats['gadai']['value']) ?>" required></label>
            <label for="stat-gadai-label">Label<input id="stat-gadai-label" name="stats[gadai][label]" maxlength="120" value="<?= escapeHtml($stats['gadai']['label']) ?>" required></label>
          </div>
          <div class="faq-item">
            <label for="stat-kitchen-value">Nilai Alfi Kitchen<input id="stat-kitchen-value" name="stats[kitchen][value]" maxlength="80" value="<?= escapeHtml($stats['kitchen']['value']) ?>" required></label>
            <label for="stat-kitchen-label">Label<input id="stat-kitchen-label" name="stats[kitchen][label]" maxlength="120" value="<?= escapeHtml($stats['kitchen']['label']) ?>" required></label>
          </div>
          <div class="actions"><button class="primary" type="submit">Simpan statistik</button></div>
        </form>
      </div>
    </section>
  <?php endif; ?>
  <?php if ($isAuthenticated): ?>
    <section class="panel faq-panel">
      <div>
        <h2>Kelola pertanyaan yang sering muncul</h2>
        <p class="form-copy">Edit isi, hapus item, atau tambahkan pertanyaan baru. Perubahan akan tampil di bagian Tanya jawab pada beranda.</p>
        <form method="post" id="faq-form">
          <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
          <input type="hidden" name="action" value="save_faq">
          <div id="faq-items">
            <?php foreach ($faqItems as $item): ?>
              <div class="faq-item">
                <label>Pertanyaan<input name="faq_questions[]" maxlength="160" value="<?= escapeHtml($item['question']) ?>" required></label>
                <label>Jawaban<textarea name="faq_answers[]" maxlength="2000" required><?= escapeHtml($item['answer']) ?></textarea></label>
                <button class="remove-faq" type="button">Hapus</button>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="actions">
            <button class="secondary" id="add-faq" type="button">Tambah pertanyaan</button>
            <button class="primary" type="submit">Simpan FAQ</button>
          </div>
        </form>
      </div>
    </section>
  <?php endif; ?>
  <?php if ($isAuthenticated): ?>
    <section class="panel images-panel">
      <h2>Kelola gambar layanan</h2>
      <p class="form-copy">Logo tampil di header, favicon, dan gambar layanan dipakai di beranda serta Tentang Kami. Format JPEG, PNG, atau WebP, maksimal 5 MB. PNG transparan disarankan untuk logo.</p>
      <div class="image-grid">
        <?php foreach ($imageSlots as $slot => $label): ?>
          <article class="image-card">
            <?php if ($imageSources[$slot] !== ''): ?>
              <img class="<?= $slot === 'logo' ? 'image-logo-preview' : '' ?>" src="<?= escapeHtml($imageSources[$slot]) ?>" alt="Pratinjau <?= escapeHtml($label) ?>">
            <?php else: ?>
              <div class="image-placeholder" aria-label="Monogram logo bawaan"><span class="mark">P</span></div>
            <?php endif; ?>
            <p class="image-status"><?= str_starts_with($imageSources[$slot], 'admin.php?image=') ? 'Gambar kustom aktif' : ($slot === 'logo' ? 'Monogram bawaan aktif' : 'Gambar bawaan aktif') ?></p>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
              <input type="hidden" name="image_slot" value="<?= escapeHtml($slot) ?>">
              <label for="image-<?= escapeHtml($slot) ?>"><?= escapeHtml($label) ?></label>
              <input id="image-<?= escapeHtml($slot) ?>" type="file" name="service_image" accept="image/jpeg,image/png,image/webp">
              <div class="image-actions">
                <button class="primary" type="submit" name="action" value="upload_image">Unggah gambar</button>
                <button class="image-reset" type="submit" name="action" value="remove_image">Gambar bawaan</button>
              </div>
            </form>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>
<footer class="wrap">CMS Pojok Berkah</footer>
<script>
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
