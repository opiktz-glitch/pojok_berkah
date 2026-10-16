<div style="margin-top:30px;">
    <h2>Gambar layanan <span class="badge badge-archive">Arsip — tidak tampil di landing</span></h2>
    <p class="form-copy">Gambar-gambar ini tampil di halaman Karya, Tentang Kami, dan sebagai gambar hias 404 — termasuk Aplikasi Parkir. Format JPEG, PNG, atau WebP, maksimal 5 MB.</p>
    <div class="image-grid">
        <?php foreach ($imageSlots as $slot => $label): ?>
            <?php if ($slot === 'logo') continue; ?>
            <article class="image-card">
                <?php if ($imageSources[$slot] !== ''): ?>
                    <img src="<?= escapeHtml($imageSources[$slot]) ?>" alt="Pratinjau <?= escapeHtml($label) ?>">
                <?php else: ?>
                    <div class="image-placeholder" aria-label="Gambar bawaan"><span class="mark">P</span></div>
                <?php endif; ?>
                <p class="image-status"><?= str_starts_with($imageSources[$slot], 'admin.php?image=') ? 'Gambar kustom aktif' : 'Gambar bawaan aktif' ?></p>
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
</div>