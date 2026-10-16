<div>
    <h2>Statistik beranda <span class="badge badge-archive">Arsip — tidak tampil di landing</span></h2>
    <p class="form-copy">Beranda korporat sudah menghapus blok statistik. Nilai tetap tersimpan bila dibutuhkan lagi.</p>
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