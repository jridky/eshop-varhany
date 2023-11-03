<main class="donation-page">
    <main class="form">
        <div class="form__heading">
            <h2 class="form__heading-text">Formulář pro adopci</h2>
            
            <div class="button ">
                <button class="button__inner " onclick="window.location='/adopce'">Zpět</button>
            </div>
        </div>
        <div class="form__wrapper">
            <div class="manuals container" style="margin-top: 2em">
                <button class="manuals__button manuals__button--active" onclick="showForm(event, 'formOS')">Fyzická osoba</button>
                <button class="manuals__button" onclick="showForm(event, 'formICO')">Právnická osoba</button>
            </div>
            <?php if($flashCount > 0){  ?>
            <div class="admin-login-page__error">
               <?= $this->Flash->render(); ?>
            </div>
            <?php } ?>
            <form class="form__content active-form" id="formOS" method="post">
                <input type="hidden" name="_method" value="post">
                <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                <input type="hidden" name="pipe_id" value="<?= $pipe['id'] ?>">
                <div class="form__container">
                    <div class="pipe-info">
                        <span class="pipe-info__heading">Vybraná píšťala:</span>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Rejstřík:</span>
                            <span class="pipe-info__item-value"><?= $pipe['rank_name'] ?></span>
                        </div>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Tón:</span>
                            <span class="pipe-info__item-value"><?= $pipe['tone_name'] ?></span>
                        </div>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Částka:</span>
                            <span class="pipe-info__item-value"><?= number_format($pipe['price'],0,',','.') ?> Kč</span>
                        </div>
                    </div>
                    <div class="form__name-row">
                        <div class="input-field">
                            <label class="input-label" for="firstName">*Jméno:</label>
                            <input id="osfirstName" class="input-field__input " name="firstName" type="text" maxlength="50" required>
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                        <div class="input-field">
                            <label class="input-label" for="lastName">*Příjmení:</label>
                            <input id="oslastName" class="input-field__input " name="lastName" type="text" maxlength="50" required>
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                    </div>
                    <div class="input-field">
                        <label class="input-label" for="email">*Email:</label>
                        <input id="email" class="input-field__input " name="email" type="email" maxlength="150" required>
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="confirmation" value="1" onclick="showHidden(this, 'os-donator')">Požaduji zaslat potvrzení o daru
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>

                    <div class="select-field os-donator">
                        <label class="input-label" for="country">*Stát:</label>
                        <select id="country" class="os-donator-item select-field__input form__state-select" name="country">
                            <option value="" disabled="">-</option>
                            <option value="Česká republika">Česká republika</option>
                            <option value="Slovensko">Slovensko</option>
                        </select>
                        <p class="select-field__error-text">&nbsp;</p>
                    </div>
                    <div class="form__city-row os-donator">
                        <div class="input-field">
                            <label class="input-label" for="city">*Město:</label>
                            <input id="city" class="os-donator-item input-field__input " name="city" type="text" maxlength="50">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                        <div class="input-field">
                            <label class="input-label" for="postcode">*PSČ:</label>
                            <input id="postcode" class="os-donator-item input-field__input " name="postcode" type="text" maxlength="20">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                    </div>
                    <div class="input-field os-donator">
                        <label class="input-label" for="address">*Ulice:</label>
                        <input id="address" class="os-donator-item input-field__input " name="address" type="text" maxlength="100">
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    <div class="input-field os-donator">
                        <label class="input-label" for="birthdate">*Datum narození:</label>
                        <input id="birthdate" class="os-donator-item input-field__input " name="birthdate" type="date" maxlength="10">
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="public" value="1" onclick="showHidden(this, 'os-public-donator'); applyName('os');">Chci být uveřejněn v seznamu dárců
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>

                    <div class="select-field os-public-donator">
                        <div class="input-field">
                            <label class="input-label" for="public">*Zobrazené jméno dárce:</label>
                            <input id="osowner" class="os-public-donator-item input-field__input " name="owner" type="text" maxlength="50">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                    </div>
                        
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="consent" required value="1">Souhlasím se zpracováním osobních údajů
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>
                </div>
                <div class="form__conformation">
                    <input class="donation-button" id="submit" type="submit" value="Zarezervovat píšťalu">
                </div>
            </form>
            
            
            <form class="form__content active-form" id="formICO" method="post" style="display: none">
                <input type="hidden" name="_method" value="post">
                <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                <input type="hidden" name="pipe_id" value="<?= $pipe['id'] ?>">
                <div class="form__container">
                    <div class="pipe-info">
                        <span class="pipe-info__heading">Vybraná píšťala:</span>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Rejstřík:</span>
                            <span class="pipe-info__item-value"><?= $pipe['rank_name'] ?></span>
                        </div>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Tón:</span>
                            <span class="pipe-info__item-value"><?= $pipe['tone_name'] ?></span>
                        </div>
                        <div class="pipe-info__item">
                            <span class="pipe-info__item-label">Částka:</span>
                            <span class="pipe-info__item-value"><?= number_format($pipe['price'],0,',','.') ?> Kč</span>
                        </div>
                    </div>

                    <div class="input-field">
                        <label class="input-label" for="copanyName">*Název společnosti:</label>
                        <input id="icocompanyName" class="input-field__input " name="companyName" type="text" maxlength="150" required>
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    <div class="input-field">
                        <label class="input-label" for="email">*Email:</label>
                        <input id="email" class="input-field__input " name="email" type="email" maxlength="150" required>
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="confirmation" value="1" onclick="showHidden(this, 'ico-donator')">Požaduji zaslat potvrzení o daru
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>

                    <div class="select-field ico-donator">
                        <label class="input-label" for="country">*Stát:</label>
                        <select id="country" class="ico-donator-item select-field__input form__state-select" name="country">
                            <option value="" disabled="">-</option>
                            <option value="Česká republika">Česká republika</option>
                            <option value="Slovensko">Slovensko</option>
                        </select>
                        <p class="select-field__error-text">&nbsp;</p>
                    </div>
                    <div class="form__city-row ico-donator">
                        <div class="input-field">
                            <label class="input-label" for="city">*Město:</label>
                            <input id="city" class="ico-donator-item input-field__input " name="city" type="text" maxlength="50">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                        <div class="input-field">
                            <label class="input-label" for="postcode">*PSČ:</label>
                            <input id="postcode" class="ico-donator-item input-field__input " name="postcode" type="text" maxlength="20">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                    </div>
                    <div class="input-field ico-donator">
                        <label class="input-label" for="address">*Ulice:</label>
                        <input id="address" class="ico-donator-item input-field__input " name="address" type="text" maxlength="100">
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    <div class="input-field ico-donator">
                        <label class="input-label" for="ico">*IČO:</label>
                        <input id="ico" class="ico-donator-item input-field__input " name="ico" type="text" maxlength="20">
                        <p class="input-field__error-text">&nbsp;</p>
                    </div>
                    
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="public" value="1" onclick="showHidden(this, 'ico-public-donator'); applyName('ico');">Chci být uveřejněn v seznamu dárců
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>

                    <div class="select-field ico-public-donator">
                        <div class="input-field">
                            <label class="input-label" for="public">*Zobrazené jméno dárce:</label>
                            <input id="icoowner" class="ico-public-donator-item input-field__input " name="owner" type="text" maxlength="50">
                            <p class="input-field__error-text">&nbsp;</p>
                        </div>
                    </div>
                        
                    <div class="form-checkbox">
                        <span class="form-checkbox__row">
                            <input class="form-checkbox__input" type="checkbox" name="consent" required value="1">Souhlasím se zpracováním osobních údajů
                        </span>
                        <p class="form-checkbox__error">&nbsp;</p>
                    </div>
                </div>
                <div class="form__conformation">
                    <input class="donation-button" id="submit" type="submit" value="Zarezervovat píšťalu">
                </div>
            </form>
        </div>
    </main>
</main>
<script>
    function showForm(evt, form) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("active-form");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].style.display = tabcontent[i].className.replace(" active-form", "");
      }
      
      tablinks = document.getElementsByClassName("manuals__button");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" manuals__button--active", "");
      }
      document.getElementById(form).style.display = "block";
      document.getElementById(form).className += " active-form";
      evt.currentTarget.className += " manuals__button--active";
    }
    
    function showHidden(element, classID) {
      parts = document.getElementsByClassName(classID);
      for (i = 0; i < parts.length; i++) {
        if(element.checked){
            parts[i].style.display = "block";
        } else {
            parts[i].style.display = "none";
        }
      }
      
      items = document.getElementsByClassName(classID + "-item");
      
      
      for (i = 0; i < items.length; i++) {
        if(element.checked){
            items[i].required = true;
        } else {
            items[i].required = false;
        }
      }
    }
    
    function applyName(prefix){
        if(prefix == "os"){
            document.getElementById(prefix + "owner").value = document.getElementById(prefix + "firstName").value + " " + document.getElementById(prefix + "lastName").value;
        } else {
            document.getElementById(prefix + "owner").value = document.getElementById(prefix + "companyName").value;
        }
        
    }
</script>

