<main class="data-page container">
    <form class="data-page__form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrfToken" value="<?= $token ?>">

        <h1 class="data-page__heading">Nahrát data z CSV souboru</h1>
        <?php if($flashCount > 0){  ?>
        <div class="admin-login-page__error">
           <?= $this->Flash->render(); ?>
        </div>
        <?php } ?>
        <input id="file" class="data-page__file-input" type="file" name="machine" accept=".csv">
        <div class="data-page__inputs">
            <label for="file" class="data-page__file-input-label ">Vybrat CSV soubor</label>
            <div class="input-field">
                <label class="input-label" for="manualName">Název stroje</label>
                <input id="manualName" class="input-field__input data-page__text-input" name="name" type="text" required>
                <p class="input-field__error-text">&nbsp;</p>
            </div>
        </div>
        <input type="submit" class="action-button  data-page__upload-button" name="load" value="Nahrát">
    </form>
    
    <div class="manual-edit">
        <h1 class="manual-edit__heading">Nahrané stroje</h1>
        <?php foreach ($machines as $m) { ?>
        <div class="manual-edit__item">
            <h2 class="manual-edit__text"><?= $m['name'] ?></h2>
            <form method="post">
                <input type="hidden" name="_method" value="post">
                <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                <input type="hidden" name="machineId" value="<?= $m['id'] ?>">
                <input type="submit" name="delete" class="action-button  manual-edit__delete-button" value="Smazat">
            </form>
        </div>
        <?php } ?>
    </div>
</main>
