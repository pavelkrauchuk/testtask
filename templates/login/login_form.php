
<h1>Требуется авторизация</h1>
<form method="post" action="/authenticate">
    <p><?= $data['message'] ?? ''; ?></p>
    <label>Логин
        <input type="text" name="login" required="required" value="<?= $data['posted']['login'] ?? ''; ?>">
    </label>
    <label>Пароль
        <input type="password" name="password" required="required">
    </label>
    <input type="submit" name="Авторизоваться">
</form>