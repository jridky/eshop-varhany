<main class="pipes-page">
    <div class="instructions container">
        <p class="instructions__text">Informace k adopci píšťaly</p>
        <button class="instructions__button" onclick="showModal()">i</button>
        <div class="modal" style="display: none">
            <div class="modal__container">
                <div class="modal__top">
                    <p class="modal__title">Instrukce k&nbsp;adopci:</p>
                    <button class="MuiButtonBase-root MuiIconButton-root" tabindex="0" type="button">
                        <span class="MuiIconButton-label" onclick="closeModal()">
                            <svg class="MuiSvgIcon-root MuiSvgIcon-colorPrimary" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>
                        </span>
                        <span class="MuiTouchRipple-root"></span>
                    </button>
                </div>
                <ol class="instructions__item-list">
                    <li class="instructions__item">Vyberte si stroj, ze kterého chcete píšťalu adoptovat (zatím jen I. manuál).</li>
                    <li class="instructions__item">Pro vybraný stroj se zobrazí tabulka píšťal. V řádcích tabulky se
                        vyskytují rejstříky (sady píšťal) stroje. Sloupce tabulky určují tón píšťaly. Píšťaly jsou barevně rozlišeny podle částky
                        (viz legenda pod výběrem stroje).</li>
                    <li class="instructions__item">Po vybrání píšťaly z tabulky se aktivuje tlačítko pro adopci píšťaly,
                        které Vás nasměruje na formulář.</li>
                    <li class="instructions__item">Ve formuláři zadáte stručné kontaktní údaje, zvolíte si jméno dárce pro zveřejnění
                        a případně doplníte údaje pro potvrzení o daru pro daňové účely.</li>
                    <li class="instructions__item">Nyní máte píšťalu <b>zarezervovanou</b> a čekáme na Vaši platbu, kterou zašlete <b>bankovním převodem</b>
                        dle instrukcí zobrazených na stránce (a zaslaných v informačním e-mailu na Vámi uvedenou adresu).
                        Prosíme o zaslání částky do jednoho týdne, jinak může dojít ke zrušení rezervace.</li>
                    <li class="instructions__item">O úspěšném doručení daru Vás budeme informovat e-mailem a Vaši píšťalu na webu označíme jako <b>adoptovanou</b>.</li>
                    <li class="instructions__item">V případě problémů nebo nejasností nás neváhejte kontaktovat na e-mailu <a href="mailto:adopce@varhanyprokrpole.cz" class="contact-info__text">adopce@varhanyprokrpole.cz</a>.</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="manuals container">
    <?php foreach($machines as $k => $m){ ?>
        <button class="manuals__button<?= ($k==0?" manuals__button--active":"") ?>" onclick="showPipes(event, 'pipes<?= $m['id'] ?>')"><?= $m['name'] ?></button>
    <?php } ?>
    </div>
    <?php if($flashCount > 0){  ?>
    <div class="admin-login-page__error">
       <?= $this->Flash->render(); ?>
    </div>
    <?php } ?>
    <div class="price-legend pipes-page__price-legend">
        <div class="__react_component_tooltip price-tooltip place-bottom type-dark" id="price-legend__tooltip" data-id="tooltip"></div>
        <div class="price-legend__container">
            <ul class="price-legend__items">
                <li class="price-legend__item" data-text="od 0 Kč do 2.000 Kč" data-for="price-legend__tooltip" onmouseover="showPriceTooltip(this)" onmouseout="hidePriceTooltip(this)" ></li>
                <li class="price-legend__item" data-text="od 2.001 Kč do 5.000 Kč" data-for="price-legend__tooltip" onmouseover="showPriceTooltip(this)" onmouseout="hidePriceTooltip(this)"></li>
                <li class="price-legend__item" data-text="od 5.001 Kč do 10.000 Kč" data-for="price-legend__tooltip" onmouseover="showPriceTooltip(this)" onmouseout="hidePriceTooltip(this)"></li>
                <li class="price-legend__item" data-text="od 10.001 Kč do 20.000 Kč" data-for="price-legend__tooltip" onmouseover="showPriceTooltip(this)" onmouseout="hidePriceTooltip(this)"></li>
                <li class="price-legend__item" data-text="nad 20.000 Kč" data-for="price-legend__tooltip" onmouseover="showPriceTooltip(this)" onmouseout="hidePriceTooltip(this)"></li>
            </ul>
            <span class="price-legend__label"> - Výše částky</span>
        </div>
        <div class="price-legend__container">
            <div class="price-legend__available"></div>
            <span class="price-legend__label"> - K adopci</span>
        </div>
        <div class="price-legend__container">
            <div class="price-legend__reserved">
                <div class="price-legend__reserved-fill">R</div>
            </div>
            <span class="price-legend__label">- Zarezervované</span>
        </div>
        <div class="price-legend__container">
            <div class="price-legend__reserved">
                <div class="price-legend__reserved-fill"></div>
            </div>
            <span class="price-legend__label">- Adoptované</span>
        </div>
    </div>
    <hr class="donation-page__line">
    <div class="__react_component_tooltip pipe-tooltip type-dark" id="pipe-table__tooltip" data-id="tooltip">
        <table>
            <tbody>
                <tr>
                    <th>Stav:</th>
                    <td id="tooltip_state">k adopci</td>
                </tr>
                <tr>
                    <th>Částka:</th>
                    <td id="tooltip_price">5.000 Kč</td>
                </tr>
                <tr>
                    <th>Píšťala:</th>
                    <td id="tooltip_pipe">Gambe 8 / ais</td>
                </tr>
                <tr id="owner-row" style="display: none">
                    <th>Adoptoval:</th>
                    <td id="tooltip_owner">Gambe 8 / ais</td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php foreach($machines as $k => $m){ ?>
    <table class="pipe-table<?= ($k == 0?" active-table":"") ?>" id="pipes<?= $m['id'] ?>"<?= ($k != 0?"style='display: none'":"") ?>>
        <thead class="pipe-table__head">
            <tr class="pipe-table__row">
                <th scope="col" class="pipe-table__heading pipe-table__heading--top pipe-table__heading--left pipe-table__heading--row-col">Rejstřík / Tón</th>
                <?php foreach($m['tones'] as $t){ ?>
                <th scope="col" class="pipe-table__heading pipe-table__heading--top"><?= $t['name'] ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody class="pipe-table__body">
            <?php foreach($m['ranks'] as $r){ ?>
            <tr class="pipe-table__row">
                <th scope="row" class="pipe-table__heading pipe-table__heading--left"><?= $r['name'] ?></th>
                <?php foreach($r['pipes'] as $key => $p){ ?>
                <td class="pipe-table__cell">
                    <div class="pipe-table__button-wrapper">
                        <button data-for="pipe-table__tooltip"  <?= ($p['state'] == 0?"onclick=\"fillDonation(this, 'pipe" . $p['id'] . "')\"":"onclick=\"unFillDonation(this)\"") ?> onmouseover="showTooltip(this)" onmouseout="hideTooltip(this)" id="pipe<?= $p['id'] ?>" 
                        rake="<?= $r['name'] ?>" pipe="<?= $m['tones'][$key]['name'] ?>" price="<?= number_format($p['price'],0,',','.') . " Kč" ?>" state="<?= $p['state'] ?>" owner="<?= $p['owner'] ?>" 
                        class="pipe-table__button pipe-table__button<?= ($p['state'] != 0?"--locked":"--available") ?>  
                        <?= ($p['price'] <= 2000?"pipe-table__button--pricepoint1":
                            ($p['price'] <= 5000?"pipe-table__button--pricepoint2":
                            ($p['price'] <= 10000?"pipe-table__button--pricepoint3":
                            ($p['price'] <= 20000?"pipe-table__button--pricepoint4":"pipe-table__button--pricepoint5")))) ?>" aria-label="Tlačítko pro výběr píšťaly">
                            <?= ($p['state'] == 1?"<div class=\"pipe-table__cell-fill\">R</div>":($p['state'] == 2?"<div class=\"pipe-table__cell-fill\"></div>":"")) ?>
                        </button>
                    </div>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
    <div class="donation-page__info-container">
        <div class="pipe-info">
            <span class="pipe-info__heading">Vybraná píšťala:</span>
                <div class="pipe-info__item">
                    <span class="pipe-info__item-label">Rejstřík:</span>
                    <span class="pipe-info__item-value" id="donationRake">---</span>
                </div>
                <div class="pipe-info__item">
                    <span class="pipe-info__item-label">Tón:</span>
                    <span class="pipe-info__item-value" id="donationTone">---</span>
                </div>
                <div class="pipe-info__item">
                    <span class="pipe-info__item-label">Částka:</span>
                    <span class="pipe-info__item-value" id="donationPrice">---</span>
                </div>
            </div>
            <div class="button donation-page__button">
                <button class="button__inner button__inner--disabled" onclick="submitForm()" id="donation-button">Adoptovat</button>
            </div>
        </div>
    </div>
    <form style="dislpay: none" action="/objednavka" method="post" id="order">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrfToken" value="<?= $token ?>">
        <input type="hidden" name="pipe" id="form_pipe">
    
    </form>
</main>
<script>
    function showPipes(evt, machine) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("active-table");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].className = tabcontent[i].className.replace(" active-table", "");
      }
      
      tablinks = document.getElementsByClassName("manuals__button");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" manuals__button--active", "");
      }
      document.getElementById(machine).style.display = "block";
      document.getElementById(machine).className += " active-table";
      evt.currentTarget.className += " manuals__button--active";
    }
    
    function fillDonation(element, pipe){
        document.getElementById("donationRake").innerHTML = document.getElementById(pipe).getAttribute("rake");
        document.getElementById("donationTone").innerHTML = document.getElementById(pipe).getAttribute("pipe");
        document.getElementById("donationPrice").innerHTML = document.getElementById(pipe).getAttribute("price");
        document.getElementById("donation-button").className = document.getElementById("donation-button").className.replace(" button__inner--disabled","");
        var selected = document.getElementsByClassName("pipe-table__button--selected");
        for (i = 0; i < selected.length; i++){
            selected[i].className = selected[i].className.replace(" pipe-table__button--selected", "");
        }
        element.className += " pipe-table__button--selected";
        document.getElementById("form_pipe").value = element.getAttribute("id").replace("pipe","");        
    }
    
    function unFillDonation(element){
        document.getElementById("donationRake").innerHTML = "---";
        document.getElementById("donationTone").innerHTML = "---";
        document.getElementById("donationPrice").innerHTML = "---";
        document.getElementById("donation-button").className = document.getElementById("donation-button").className.replace(" button__inner--disabled","");
        document.getElementById("donation-button").className += " button__inner--disabled";
        var selected = document.getElementsByClassName("pipe-table__button--selected");
        for (i = 0; i < selected.length; i++){
            selected[i].className = selected[i].className.replace(" pipe-table__button--selected", "");
        }
        element.className += " pipe-table__button--selected";
        document.getElementById("form_pipe").value = "";        
    }
    
    function submitForm(){
        if(document.getElementById("form_pipe").value != ""){
            document.getElementById("order").submit();
        }
    }
    
    function showTooltip(element){
        var state, owner;
        switch(element.getAttribute("state")){
            case '2': state = "adoptovaná";
                if(element.getAttribute("owner") == ""){
                    owner = "anonymně";
                } else {
                    owner = element.getAttribute("owner");
                }
                break;
            case '1': state = "rezervovaná";
                if(element.getAttribute("owner") == ""){
                    owner = "anonymně";
                } else {
                    owner = element.getAttribute("owner");
                }
                break;
            default: state = "k adopci";
                owner = undefined;
                break;
        }
        document.getElementById("tooltip_pipe").innerHTML = element.getAttribute("rake") + " / " + element.getAttribute("pipe");
        document.getElementById("tooltip_price").innerHTML = element.getAttribute("price");
        document.getElementById("tooltip_state").innerHTML = state;

        if(owner != undefined){
            document.getElementById("tooltip_owner").innerHTML = owner;
            document.getElementById("owner-row").style.display = "table-row";
        }
        
        var page = document.getElementsByClassName("pipes-page")[0];
        var table = document.getElementsByClassName("active-table")[0];
        var html = document.getElementsByTagName("html")[0];
        
        var left = element.offsetLeft - (document.getElementById("pipe-table__tooltip").offsetWidth/2) + (element.offsetWidth/2) - table.scrollLeft;
        if((left + document.getElementById("pipe-table__tooltip").offsetWidth) > (page.offsetLeft + page.offsetWidth)){
            document.getElementById("pipe-table__tooltip").style.top = (element.offsetTop - (document.getElementById("pipe-table__tooltip").offsetHeight/2) + (element.parentNode.offsetHeight/2) - html.scrollTop) + "px";
            document.getElementById("pipe-table__tooltip").style.left = (element.offsetLeft - document.getElementById("pipe-table__tooltip").offsetWidth - (element.offsetWidth/2) - table.scrollLeft) + "px";
            document.getElementById("pipe-table__tooltip").className += " place-left";
        } else if (left < 1) {
            document.getElementById("pipe-table__tooltip").style.top = (element.offsetTop - (document.getElementById("pipe-table__tooltip").offsetHeight/2) + (element.parentNode.offsetHeight/2) - html.scrollTop) + "px";
            document.getElementById("pipe-table__tooltip").style.left = (element.offsetLeft - table.scrollLeft + element.parentNode.offsetWidth) + "px";
            document.getElementById("pipe-table__tooltip").className += " place-right";
        } else {
            var height = (element.offsetTop + element.parentNode.offsetHeight - html.scrollTop);
            if((height + document.getElementById("pipe-table__tooltip").offsetHeight) > window.innerHeight){
                document.getElementById("pipe-table__tooltip").style.top = (element.offsetTop - document.getElementById("pipe-table__tooltip").offsetHeight - html.scrollTop - 10) + "px";
                document.getElementById("pipe-table__tooltip").style.left = (element.offsetLeft - (document.getElementById("pipe-table__tooltip").offsetWidth/2) + (element.offsetWidth/2) - table.scrollLeft) + "px";
                document.getElementById("pipe-table__tooltip").className += " place-top";       
            } else {
                document.getElementById("pipe-table__tooltip").style.top = (element.offsetTop + element.parentNode.offsetHeight - html.scrollTop) + "px";
                document.getElementById("pipe-table__tooltip").style.left = (element.offsetLeft - (document.getElementById("pipe-table__tooltip").offsetWidth/2) + (element.offsetWidth/2) - table.scrollLeft) + "px";
                document.getElementById("pipe-table__tooltip").className += " place-bottom";
            }
        }
        document.getElementById("pipe-table__tooltip").className += " show";
        
    }
    
    function hideTooltip(element){
        document.getElementById("pipe-table__tooltip").className = document.getElementById("pipe-table__tooltip").className.replace(" show", "");
        document.getElementById("pipe-table__tooltip").className = document.getElementById("pipe-table__tooltip").className.replace(" place-left", "");
        document.getElementById("pipe-table__tooltip").className = document.getElementById("pipe-table__tooltip").className.replace(" place-right", "");        
        document.getElementById("pipe-table__tooltip").className = document.getElementById("pipe-table__tooltip").className.replace(" place-bottom", "");
        document.getElementById("pipe-table__tooltip").style.top = "-999em";
        document.getElementById("pipe-table__tooltip").style.left = "-999em";
        document.getElementById("owner-row").style.display = "none";
    }
    
    function showPriceTooltip(element){
        document.getElementById("price-legend__tooltip").innerHTML = element.getAttribute("data-text");
        document.getElementById("price-legend__tooltip").style.top = (element.offsetTop + element.parentNode.offsetHeight) + "px";
        document.getElementById("price-legend__tooltip").style.left = (element.offsetLeft - (document.getElementById("price-legend__tooltip").offsetWidth/2) + (element.offsetWidth/2)) + "px";
        document.getElementById("price-legend__tooltip").className += " place-bottom";
        document.getElementById("price-legend__tooltip").className += " show";
    }
    
    function hidePriceTooltip(element){
        document.getElementById("price-legend__tooltip").className = document.getElementById("price-legend__tooltip").className.replace(" show", "");
        document.getElementById("price-legend__tooltip").className = document.getElementById("price-legend__tooltip").className.replace(" place-top", "");
        document.getElementById("price-legend__tooltip").className = document.getElementById("price-legend__tooltip").className.replace(" place-bottom", "");
        document.getElementById("price-legend__tooltip").style.top = "-999em";
        document.getElementById("price-legend__tooltip").style.left = "-999em";
    }
    
    function showModal(){
        document.getElementsByClassName("modal")[0].style.display = "flex";
    }
    
    function closeModal(){
        document.getElementsByClassName("modal")[0].style.display = "none";
    }
</script>
