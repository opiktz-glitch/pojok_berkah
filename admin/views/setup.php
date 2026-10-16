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