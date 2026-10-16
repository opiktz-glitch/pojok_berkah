<div>
    <h2>Pertanyaan umum <span class="badge badge-live">Dipakai landing</span></h2>
    <p class="form-copy">Edit isi, hapus item, atau tambahkan pertanyaan baru. Perubahan tampil di bagian Tanya jawab beranda. Gunakan bahasa umum korporat, bukan nama produk.</p>
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
            <?php if (count($faqItems) < 20): ?>
                <a class="secondary button" href="admin.php?tab=faq&amp;faq_extra=<?= (int)($faqExtra + 1) ?>">Tambah baris (tanpa JS)</a>
            <?php endif; ?>
            <button class="primary" type="submit">Simpan FAQ</button>
        </div>
        <p class="hint">Tanpa JavaScript: gunakan "Tambah baris (tanpa JS)". Untuk menghapus item, kosongkan kolom pertanyaan/jawabannya lalu klik Simpan FAQ.</p>
    </form>
</div>