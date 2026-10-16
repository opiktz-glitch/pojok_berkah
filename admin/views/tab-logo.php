<div>
    <h2>Logo situs <span class="badge badge-live">Dipakai landing</span></h2>
    <p class="form-copy">Logo tampil di header dan favicon beranda. Format JPEG, PNG, atau WebP, maksimal 5 MB. PNG transparan disarankan.</p>
    <div class="logo-card">
        <div>
            <?php if ($imageSources['logo'] !== ''): ?>
                <img src="<?= escapeHtml($imageSources['logo']) ?>" alt="Pratinjau Logo situs">
            <?php else: ?>
                <div class="image-placeholder" aria-label="Monogram logo bawaan"><span class="mark">P</span></div>
            <?php endif; ?>
            <p class="image-status"><?= str_starts_with($imageSources['logo'], 'admin.php?image=') ? 'Gambar kustom aktif' : 'Monogram bawaan aktif' ?></p>
        </div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
            <input type="hidden" name="image_slot" value="logo">
            <label for="image-logo">Logo situs</label>
            <input id="image-logo" type="file" name="service_image" accept="image/jpeg,image/png,image/webp">
            <div class="image-actions">
                <button class="primary" type="submit" name="action" value="upload_image">Unggah gambar</button>
                <button class="image-reset" type="submit" name="action" value="remove_image">Gambar bawaan</button>
            </div>
        </form>
    </div>
</div>