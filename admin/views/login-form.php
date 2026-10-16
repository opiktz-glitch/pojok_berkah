<h2>Masuk ke CMS</h2>
<p class="form-copy">Masukkan password admin untuk melanjutkan.</p>
<form method="post" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= escapeHtml($csrf) ?>">
    <input type="hidden" name="action" value="login">
    <label for="password">Password admin</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required autofocus>
    <div class="actions"><button class="primary" type="submit">Masuk</button></div>
</form>