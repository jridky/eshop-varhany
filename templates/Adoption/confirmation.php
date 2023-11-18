<main class="bank-transfer-page container">
    <h1 class="bank-transfer-page__heading">Níže neleznete instrukce pro dokončení platby</h1>
    <p class="bank-transfer-page__info">Tyto informace Vám byly zaslány i na uvedený e-mail</p>
    <section class="bank-transfer-page__instructions-row">
        <table class="bank-transfer-page__instructions">
            <tbody>
                <tr>
                    <th>Bankovní účet:</th>
                    <td>249610162/0300</td>
                </tr>
                <tr>
                    <th>Variabilní symbol:</th>
                    <td><?= $data['id'] ?></td>
                </tr>
                <tr>
                    <th>Částka:</th>
                    <td><?= number_format($data['price'],0,',','.') ?> Kč</td>
                </tr>
                <tr>
                    <th style="vertical-align: top">QR Platba</th>
                    <td>
                        <img src="https://chart.googleapis.com/chart?cht=qr&chs=200x200&choe=ISO-8859-1&chl=<?= urlencode("SPD*1.0*ACC:CZ2303000000000249610162*CC:CZK*AM:" . $data['price'] . "*X-VS:" . $data['id'] . "*RN:Varhany pro Kr.Pole*MSG:Adopce pistaly - dekujeme za vasi podporu")?>">
                    </td>
            </tbody>
        </table>

        <p class="bank-transfer-page__instructions-text">
            Vybranou píšťalu jsme pro Vás zarezervovali. Pro dokončení příspěvku zašlete částku na uvedený bankovní účet
            s variabilním symbolem. Děkujeme.
        </p>
    </section>
    <div class="bank-transfer-page__warning">
        <div class="bank-transfer-page__warning-icon"></div>
        <p class="bank-transfer-page__warning-text">
            Prosíme o zaslání částky do jednoho týdne, jinak může dojít ke zrušení rezervace.
        </p>
    </div>
    <a class="bank-transfer-page__button round-button" href="/adopce">Hotovo</a>
</main>
