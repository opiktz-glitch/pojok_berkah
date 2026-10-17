<div>
    <h2>Pertanyaan umum <span class="badge badge-live">Dipakai landing</span></h2>
    <p class="form-copy">Edit isi, hapus item, atau tambahkan pertanyaan baru. Perubahan tampil di bagian Tanya jawab beranda. Gunakan bahasa umum korporat, bukan nama produk.</p>
    <form method="post" id="faq-form">
        <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
        <input type="hidden" name="action" value="save_faq">
        <div id="faq-items">
            <?php foreach ($faqItems as $index => $item): ?>
                <div class="faq-item">
                    <label>Pertanyaan<input name="faq_questions[]" maxlength="160" value="<?= escapeHtml($item['question']) ?>"></label>
                    <label>Jawaban<textarea name="faq_answers[]" maxlength="2000"><?= escapeHtml($item['answer']) ?></textarea></label>
                    <label class="remove-faq"><input type="checkbox" name="faq_remove[]" value="<?= (int)$index ?>"> Hapus saat disimpan</label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <?php if (count($faqItems) < 20): ?>
                <a class="secondary button" href="admin.php?tab=faq&amp;faq_extra=<?= (int)($faqExtra + 1) ?>">Tambah baris</a>
            <?php endif; ?>
            <button class="primary" type="submit">Simpan FAQ</button>
        </div>
        <p class="hint">Baris yang pertanyaan atau jawabannya kosong tidak disimpan. Centang "Hapus saat disimpan" untuk membuang item. Klik "Simpan FAQ" dulu sebelum "Tambah baris" agar isian yang sedang diedit tidak hilang.</p>
    </form>
</div>
