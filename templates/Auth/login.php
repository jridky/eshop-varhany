<div class="admin-login-page">
    <form class="admin-login-page__container" method="post">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrfToken" value="<?= $token ?>">
        <h1 class="admin-login-page__login-title">Přihlášení do Administrace</h1>
        <div class="input-field">
            <label class="input-label" for="username">Uživatelské jméno</label>
            <input id="username" class="input-field__input " name="username" type="text">
            <p class="input-field__error-text">&nbsp;</p>
        </div>
        <div class="input-field">
            <label class="input-label" for="password">Heslo</label>
            <input id="password" class="input-field__input " name="password" type="password">
            <p class="input-field__error-text">&nbsp;</p>
        </div>
        <div class="admin-login-page__button">
            <input type="submit" name="login" value="Přihlásit" class="action-button">
        </div>
    </form>
</div>
