<main class="contact-page container">
    <h1 class="contact-page__heading">Kontaktujte nás</h1>
    <div class="contact-page__container">
        <form action="" class="contact-page__form" method="post">
            <input type="hidden" name="_method" value="post">
            <input type="hidden" name="_csrfToken" value="<?= $token ?>">
            <div class="input-field">
                <label class="input-label" for="name">Jméno</label>
                <input id="name" class="input-field__input contact-page__small-input" name="name" type="text" maxlength="100" required>
                <p class="input-field__error-text">&nbsp;</p>
            </div>
            <div class="input-field">
                <label class="input-label" for="email">Email</label>
                <input id="email" class="input-field__input contact-page__small-input" name="email" type="email" maxlength="150" required>
                <p class="input-field__error-text">&nbsp;</p>
            </div>
            <label class="input-label">Zpráva</label>
            <textarea name="message" class="contact-page__message" rows="7" maxlength="500" required></textarea>
            <p class="input-field__error-text"></p>
            <input type="submit" value="Odeslat" class="action-button contact-page__button">
        </form>
        <div class="contact-info">
            <div class="contact-info__items">
                <div class="contact-info__item">
                    <div class="contact-info__row">
                        <svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"></path></svg>
                        <span class="contact-info__item-heading">Email:</span>
                    </div>
                    <a href="mailto:adopce@varhanyprokrpole.cz" class="contact-info__text">adopce@varhanyprokrpole.cz</a>
                </div>
                <div class="contact-info__item">
                    <div class="contact-info__row">
                        <svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"></path></svg>
                        <span class="contact-info__item-heading">Telefon:</span>
                    </div>
                    <p class="contact-info__text">+420 603 893 162</p>
                </div>
                <div class="contact-info__item">
                    <div class="contact-info__row">
                        <svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"></path></svg>
                        <span class="contact-info__item-heading">Hlavní stránka:</span>
                    </div>
                    <a class="contact-info__text" href="https://www.varhanyprokrpole.cz/">https://www.varhanyprokrpole.cz/</a>
                </div>
                <div class="contact-info__item">
                    <div class="contact-info__row">
                        <svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path></svg>
                        <span class="contact-info__item-heading">Adresa:</span>
                    </div>
                    <p class="contact-info__text">Brno, Královo Pole, Nová 20</p>
                </div>
            </div>
        </div>
    </div>
</main>
