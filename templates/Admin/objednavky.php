<main class="orders">
    <div class="orders__head container">
        <h2 class="orders__heading">Objednávky</h2>
    </div>
    <div class="manuals container" style="margin-bottom: 2em">
        <button class="manuals__button manuals__button--active" onclick="showOrders(event, 'waiting')">Nové adopce</button>
        <button class="manuals__button" onclick="showOrders(event, 'done')">Vyřízené adopce</button>
        <button class="manuals__button" onclick="showOrders(event, 'cancelled')">Zrušené adopce</button>  
        <?php if($flashCount > 0){  ?>
        <div class="admin-login-page__error">
           <?= $this->Flash->render(); ?>
        </div>
        <?php } ?>
    </div>
    <div class="__react_component_tooltip pipe-tooltip place-bottom type-dark" id="order-table__tooltip" data-id="tooltip">Potvrdit objednávku</div>
    <div class="table__wrapper active-table" id="waiting">
        <div class="table__container">
            <table class="table orderTable">
                <thead>
                    <tr class="table__header">
                        <th class="table__heading">VS</th>
                        <th class="table__heading">Platba</th>
                        <th class="table__heading">Stav</th>
                        <th class="table__heading">Vytvořeno</th>
                        <th class="table__heading">Aktualizováno</th>
                        <th class="table__heading">Píšťala</th>
                        <th class="table__heading">Částka</th>
                        <th class="table__heading">Dárce</th>
                        <th class="table__heading">Akce</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($unprocessed as $u) { ?>
                    <tr class="table__row">
                        <td class="table__cell"><?= $u['id'] ?></td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--orange">čekající</span>
                        </td>
                        <td class="table__cell"><?= $u['created'] ?></td>
                        <td class="table__cell"><?= $u['updated'] ?></td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/<?= $u['pipe_id'] ?>"><?= $u['pipe_name'] ?></a></td>
                        <td class="table__cell"><?= number_format($u['pipe_price'],0,',','.') ?> Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator"><?= ($u['first_name'] != ""?$u['first_name'] . " " . $u['last_name']:$u['company_name']) ?></div>
                        </td>
                        <td class="table__cell">
                            <form method="post">
                            <input type="hidden" name="_method" value="post">
                            <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                            <input type="hidden" name="order_id" value="<?= $u['id'] ?>">
                            <div class="order-table__actions">
                                <button type="submit" name="confirm" class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeSmall" tabindex="0" data-tip="Potvrdit objednávku" data-for="order-table__tooltip" aria-label="Potvrdit objednávku">
                                    <span class="MuiIconButton-label">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall" focusable="false" viewBox="0 0 24 24" aria-hidden="true" style="color: green;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path></svg>
                                    </span>
                                    <span class="MuiTouchRipple-root"></span>
                                </button>
                                <button type="submit" name="reject" class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeSmall" tabindex="0" data-tip="Zrušit objednávku" data-for="order-table__tooltip" aria-label="Zrušit objednávku">
                                    <span class="MuiIconButton-label">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall" focusable="false" viewBox="0 0 24 24" aria-hidden="true" style="color: red;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>
                                    </span>
                                    <span class="MuiTouchRipple-root"></span>
                                </button>
                            </div>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="table__wrapper" id="done" style="display: none">
        <div class="table__container">
            <table class="table orderTable">
                <thead>
                    <tr class="table__header">
                        <th class="table__heading">VS</th>
                        <th class="table__heading">Platba</th>
                        <th class="table__heading">Stav</th>
                        <th class="table__heading">Vytvořeno</th>
                        <th class="table__heading">Aktualizováno</th>
                        <th class="table__heading">Píšťala</th>
                        <th class="table__heading">Částka</th>
                        <th class="table__heading">Dárce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($processed as $u) { ?>
                    <tr class="table__row">
                        <td class="table__cell"><?= $u['id'] ?></td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--green">úspěšná</span>
                        </td>
                        <td class="table__cell"><?= $u['created'] ?></td>
                        <td class="table__cell"><?= $u['updated'] ?></td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/<?= $u['pipe_id'] ?>"><?= $u['pipe_name'] ?></a></td>
                        <td class="table__cell"><?= number_format($u['pipe_price'],0,',','.') ?> Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator"><?= ($u['first_name'] != ""?$u['first_name'] . " " . $u['last_name']:$u['company_name']) ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="table__wrapper" id="cancelled" style="display: none">
        <div class="table__container">
            <table class="table orderTable">
                <thead>
                    <tr class="table__header">
                        <th class="table__heading">VS</th>
                        <th class="table__heading">Platba</th>
                        <th class="table__heading">Stav</th>
                        <th class="table__heading">Vytvořeno</th>
                        <th class="table__heading">Aktualizováno</th>
                        <th class="table__heading">Píšťala</th>
                        <th class="table__heading">Částka</th>
                        <th class="table__heading">Dárce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cancelled as $u) { ?>
                    <tr class="table__row">
                        <td class="table__cell"><?= $u['id'] ?></td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--red">zrušená</span>
                        </td>
                        <td class="table__cell"><?= $u['created'] ?></td>
                        <td class="table__cell"><?= $u['updated'] ?></td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/<?= $u['pipe_id'] ?>"><?= $u['pipe_name'] ?></a></td>
                        <td class="table__cell"><?= number_format($u['pipe_price'],0,',','.') ?> Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator"><?= ($u['first_name'] != ""?$u['first_name'] . " " . $u['last_name']:$u['company_name']) ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    $(document).ready(function (){
        $(".orderTable").DataTable({
            language: {
                "sEmptyTable":     "Tabulka neobsahuje žádná data",
                "sInfo":           "_START_ až _END_ z _TOTAL_ záznamů",
                "sInfoEmpty":      "0 až 0 z 0 záznamů",
                "sInfoFiltered":   "(filtrováno)",
                "sInfoPostFix":    "",
                "sInfoThousands":  " ",
                "sLengthMenu":     "Zobraz záznamů _MENU_",
                "sLoadingRecords": "Načítám...",
                "sProcessing":     "Provádím...",
                "sSearch":         "Hledat:",
                "sZeroRecords":    "Žádné záznamy nebyly nalezeny",
                "oPaginate": {
                    "sFirst":    "První",
                    "sLast":     "Poslední",
                    "sNext":     "Další",
                    "sPrevious": "Předchozí"
                },
                "oAria": {
                    "sSortAscending":  ": aktivujte pro řazení sloupce vzestupně",
                    "sSortDescending": ": aktivujte pro řazení sloupce sestupně"
                }
            }
        });
    });
    
    function showOrders(evt, type) {
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
      document.getElementById(type).style.display = "flex";
      document.getElementById(type).className += " active-table";
      evt.currentTarget.className += " manuals__button--active";
    }
</script>
