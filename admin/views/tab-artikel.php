<?php
if (!defined('POJOK_BERKAH_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}
$allArticles = isset($settings['articles']) && is_array($settings['articles']) ? $settings['articles'] : [];
$editSlug = isset($_GET['edit_artikel']) && is_string($_GET['edit_artikel']) ? trim($_GET['edit_artikel']) : '';
$viewSlug = isset($_GET['view_artikel']) && is_string($_GET['view_artikel']) ? trim($_GET['view_artikel']) : '';
$editing = null;
$viewing = null;
foreach ($allArticles as $article) {
    if (!is_array($article)) { continue; }
    if (($article['slug'] ?? '') === $editSlug) { $editing = $article; }
    if (($article['slug'] ?? '') === $viewSlug) { $viewing = $article; }
}
$editingSlugVal = $editing['slug'] ?? '';
$editingImage = (string)($editing['image'] ?? '');
if (!preg_match('~^data:image/(?:jpeg|png|webp);base64,[A-Za-z0-9+/=]+$~', $editingImage)) { $editingImage = ''; }
$draft = null;
if ($editing === null && isset($_SESSION['artikel_draft']) && is_array($_SESSION['artikel_draft'])) {
  $draft = $_SESSION['artikel_draft'];
}
unset($_SESSION['artikel_draft']);
$viewingImage = (string)($viewing['image'] ?? '');
if (!preg_match('~^data:image/(?:jpeg|png|webp);base64,[A-Za-z0-9+/=]+$~', $viewingImage)) { $viewingImage = ''; }
?>
<div class="panel-head">
  <h2>Artikel <span class="badge-live">Pengelolaan artikel</span></h2>
  <p class="muted">Tulis artikel langsung atau ambil isi dari file HTML, Markdown, atau teks. File dibaca di server lalu dimasukkan ke formulir agar bisa ditinjau sebelum disimpan.</p>
</div>

<?php if ($viewing !== null): ?>
  <section class="article-preview-card" aria-labelledby="article-preview-title">
    <div class="article-preview-head"><div><span class="hint">Pratinjau artikel · <?= escapeHtml(($viewing['status'] ?? 'draft') === 'published' ? 'Published' : 'Draft') ?></span><h3 id="article-preview-title"><?= escapeHtml($viewing['title'] ?? '') ?></h3></div><a class="button secondary" href="admin.php?tab=artikel">Tutup pratinjau</a></div>
    <p class="article-preview-excerpt"><?= escapeHtml($viewing['excerpt'] ?? '') ?></p>
    <?php if ($viewingImage !== ''): ?><img class="article-preview-cover" src="<?= escapeHtml($viewingImage) ?>" alt="<?= escapeHtml($viewing['image_alt'] ?? '') ?>"><?php endif; ?>
    <div class="article-preview-content"><?= escapeHtml($viewing['content'] ?? '') ?></div>
  </section>
<?php endif; ?>

<section class="article-editor" aria-labelledby="article-form-title">
  <div class="article-editor-head">
    <div><h3 id="article-form-title"><?= $editing !== null ? 'Edit artikel' : 'Tambah artikel' ?></h3><p class="form-copy"><?= $editing !== null ? 'Perbarui informasi artikel lalu simpan perubahan.' : 'Pilih cara menyiapkan artikel. Kamu bisa menyunting hasil impor sebelum menyimpannya.' ?></p></div>
    <?php if ($editing !== null): ?><a class="button secondary" href="admin.php?tab=artikel">Batal mengedit</a><?php endif; ?>
  </div>

  <?php if ($editing === null): ?>
    <form method="post" enctype="multipart/form-data" class="article-file-box">
      <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
      <input type="hidden" name="action" value="import_artikel">
      <label for="article-file">Ambil isi dari file <span class="hint-inline">(opsional)</span>
        <input id="article-file" name="artikel_file" type="file" accept=".html,.htm,.md,.markdown,.txt,text/html,text/plain,text/markdown" required>
      </label>
      <p class="hint">Format HTML, MD, atau TXT · Maksimal 2 MB. Isi file dibaca di server lalu dimasukkan ke formulir di bawah; belum tersimpan sebelum kamu menekan "Simpan artikel".</p>
      <div class="actions"><button type="submit" class="secondary">Isi formulir dari file</button></div>
    </form>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="stack article-form" id="article-form">
    <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
    <input type="hidden" name="action" value="save_artikel">
    <input type="hidden" name="artikel_editing_slug" value="<?= escapeHtml($editingSlugVal) ?>">
    <label>Judul artikel
      <input type="text" name="artikel_title" value="<?= escapeHtml($editing['title'] ?? ($draft['title'] ?? '')) ?>" minlength="5" maxlength="120" required placeholder="Contoh: Mengenal Sistem Parkir Kamera Otomatis">
    </label>
    <label>Slug URL <span class="hint-inline">(boleh dikosongkan, dibuat otomatis dari judul)</span>
      <input type="text" name="artikel_slug" value="<?= escapeHtml($editingSlugVal) ?>" maxlength="60" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="parkir-kamera-otomatis">
      <span class="hint">Huruf kecil, angka, dan tanda hubung.</span>
    </label>
    <label>Ringkasan
      <textarea name="artikel_excerpt" minlength="20" maxlength="320" required rows="3" placeholder="Ringkasan singkat untuk daftar artikel dan hasil pencarian."><?= escapeHtml($editing['excerpt'] ?? ($draft['excerpt'] ?? '')) ?></textarea>
      <span class="hint">20–320 karakter.</span>
    </label>
    <label>Isi artikel
      <textarea name="artikel_content" minlength="100" maxlength="30000" required rows="14" placeholder="Tulis artikel di sini. Pisahkan paragraf dengan baris kosong."><?= escapeHtml($editing['content'] ?? ($draft['content'] ?? '')) ?></textarea>
      <span class="hint">Teks biasa · 100–30.000 karakter. Baris baru akan dipertahankan di halaman artikel.</span>
    </label>
    <div class="article-image-field">
      <label for="artikel-image">Gambar utama <span class="hint-inline">(opsional)</span>
        <input type="file" id="artikel-image" name="artikel_image" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
      </label>
      <span class="hint">JPG, PNG, atau WebP · Maksimal 2 MB. Rasio lanskap sekitar 1200 × 630 px disarankan.</span>
      <label for="artikel-image-alt">Teks alternatif gambar
        <input type="text" id="artikel-image-alt" name="artikel_image_alt" maxlength="180" value="<?= escapeHtml($editing['image_alt'] ?? '') ?>" placeholder="Contoh: Kamera di gerbang masuk area parkir">
      </label>
      <span class="hint">Jelaskan isi gambar secara singkat untuk pembaca dan teknologi bantu.</span>
      <?php if ($editingImage !== ''): ?>
        <label class="article-remove-image"><input type="checkbox" name="artikel_remove_image" value="1"> Hapus gambar utama saat menyimpan</label>
      <?php endif; ?>
      <?php if ($editingImage !== ''): ?>
      <div class="article-image-preview has-image">
        <img src="<?= escapeHtml($editingImage) ?>" alt="Gambar utama artikel saat ini">
      </div>
      <?php endif; ?>
    </div>
    <label>Status publikasi
      <select name="artikel_status">
        <option value="draft"<?= ($editing['status'] ?? 'draft') === 'draft' ? ' selected' : '' ?>>Draft — hanya terlihat di admin</option>
        <option value="published"<?= ($editing['status'] ?? '') === 'published' ? ' selected' : '' ?>>Terbitkan — tampil di situs dan sitemap</option>
      </select>
    </label>
    <div class="actions"><button type="submit" class="primary"><?= $editing !== null ? 'Simpan perubahan' : 'Simpan artikel' ?></button><?php if ($editing !== null): ?><a class="button secondary" href="admin.php?tab=artikel">Batal</a><?php endif; ?></div>
  </form>
</section>

<section class="article-list-section" aria-labelledby="article-list-title">
  <div class="article-list-head"><div><h3 id="article-list-title">Artikel tersimpan</h3><p class="form-copy"><?= count($allArticles) ?> artikel</p></div></div>
  <?php if (empty($allArticles)): ?>
    <div class="article-empty"><strong>Belum ada artikel.</strong><span>Mulai dengan menulis manual atau mengambil isi dari file.</span></div>
  <?php else: ?>
    <div class="article-table-wrap"><table class="article-table"><thead><tr><th scope="col">Judul</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead><tbody>
      <?php foreach ($allArticles as $a): if (!is_array($a)) { continue; } $slug = (string)($a['slug'] ?? ''); $title = (string)($a['title'] ?? 'Tanpa judul'); $status = (string)($a['status'] ?? 'draft'); $publicUrl = 'artikel/' . rawurlencode($slug) . '.html'; ?>
        <tr>
          <td class="article-table-title"><?= escapeHtml($title) ?></td>
          <td><span class="article-status<?= $status === 'published' ? ' is-published' : '' ?>"><?= $status === 'published' ? 'Terbit' : 'Draft' ?></span></td>
          <td><div class="article-row-actions">
            <?php if ($status === 'published'): ?><a class="button secondary article-action" href="<?= escapeHtml($publicUrl) ?>" target="_blank" rel="noopener">Lihat</a><?php else: ?><a class="button secondary article-action" href="admin.php?tab=artikel&amp;view_artikel=<?= rawurlencode($slug) ?>">Lihat</a><?php endif; ?>
            <a class="button secondary article-action" href="admin.php?tab=artikel&amp;edit_artikel=<?= rawurlencode($slug) ?>">Edit</a>
            <form method="post" class="article-delete-form">
              <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>"><input type="hidden" name="action" value="delete_artikel"><input type="hidden" name="artikel_slug" value="<?= escapeHtml($slug) ?>">
              <label class="article-confirm"><input type="checkbox" name="confirm_delete" value="1" required> Yakin hapus</label>
              <button class="secondary article-action article-delete" type="submit">Hapus</button>
            </form>
          </div></td>
        </tr>
      <?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</section>


