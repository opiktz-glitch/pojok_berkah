<div>
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