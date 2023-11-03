
        <?php if($flashCount > 0){  ?>
        <div class="admin-login-page__error">
           <?= $this->Flash->render(); ?>
        </div>
        <?php } ?>

<main class="orders">
    <div class="orders__head container">
        <h2 class="orders__heading">Objednávky</h2>
    </div>
    <div class="__react_component_tooltip pipe-tooltip place-bottom type-dark" id="order-table__tooltip" data-id="tooltip">Potvrdit objednávku</div>
    <div class="table__wrapper">
        <div class="table__container">
            <table class="table" id="orderTable">
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
                    <tr class="table__row">
                        <td class="table__cell">25</td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--red">zrušená</span>
                        </td>
                        <td class="table__cell">03.11.2023 10:57</td>
                        <td class="table__cell">03.11.2023 23:28</td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/291">test, Prestant 8, D</a></td>
                        <td class="table__cell">25.000 Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator">Josef Řídký</div>
                        </td>
                        <td class="table__cell"></td>
                    </tr>
                    <tr class="table__row">
                        <td class="table__cell">22</td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--green">úspěšná</span>
                        </td>
                        <td class="table__cell">29.10.2023 19:33</td>
                        <td class="table__cell">03.11.2023 23:32</td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/205">test, Trompete 16, c</a></td>
                        <td class="table__cell">2.500 Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator">Vojtěch Řehák</div>
                        </td>
                        <td class="table__cell"></td>
                    </tr>
                    <tr class="table__row">
                        <td class="table__cell">21</td>
                        <td class="table__cell">
                            <div class="order-table__payment">
                                <span>převodem</span>
                                <div></div>
                            </div>
                        </td>
                        <td class="table__cell">
                            <span class="order-table__state order-table__state--orange">čekající</span>
                        </td>
                        <td class="table__cell">25.10.2023 10:41</td>
                        <td class="table__cell">25.10.2023 10:41</td>
                        <td class="table__cell"><a class="order-table__pipe-text" href="/admin/pistaly/253">test, Montre 16, c</a></td>
                        <td class="table__cell">2.500 Kč</td>
                        <td class="table__cell">
                            <div class="order-table__donator">Vojtěch Řehák</div>
                        </td>
                        <td class="table__cell">
                            <div class="order-table__actions">
                                <button class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeSmall" tabindex="0" type="button" data-tip="Potvrdit objednávku" data-for="order-table__tooltip" aria-label="Potvrdit objednávku" currentitem="false">
                                    <span class="MuiIconButton-label">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall" focusable="false" viewBox="0 0 24 24" aria-hidden="true" style="color: green;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path></svg>
                                    </span>
                                    <span class="MuiTouchRipple-root"></span>
                                </button>
                                <button class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeSmall" tabindex="0" type="button" data-tip="Zrušit objednávku" data-for="order-table__tooltip" aria-label="Zrušit objednávku" currentitem="false">
                                    <span class="MuiIconButton-label">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall" focusable="false" viewBox="0 0 24 24" aria-hidden="true" style="color: red;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg>
                                    </span>
                                    <span class="MuiTouchRipple-root"></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    $(document).ready(function (){
        $("#orderTable").DataTable({
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
</script>
