<div>
    <h2>Nomor admin <span class="badge badge-live">Dipakai landing</span></h2>
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
</div>
<aside class="preview">
    <span>Nomor aktif</span>
    <strong>+<?= escapeHtml($waNumber) ?></strong>
    <a href="https://wa.me/<?= escapeHtml($waNumber) ?>" target="_blank" rel="noopener">Tes tautan WhatsApp</a>
    <p class="notice">Perubahan tersimpan di server dan digunakan oleh semua tombol WhatsApp pada situs.</p>
</aside>