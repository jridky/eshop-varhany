<main class="data-page container">
    <?php if($flashCount > 0){  ?>
    <div class="admin-login-page__error">
       <?= $this->Flash->render(); ?>
    </div>
    <?php } ?>

    <?php if(isset($pendingUpdate)): ?>
    <div class="manual-edit">
        <h1 class="manual-edit__heading">Náhled změn – <?= h($pendingUpdate['machineName']) ?></h1>
        <?php if(empty($pendingUpdate['ranks']) && empty($pendingUpdate['warnings'])): ?>
            <p>Žádné změny k provedení.</p>
        <?php else: ?>
            <?php foreach($pendingUpdate['ranks'] as $rank): ?>
            <div class="manual-edit__preview-rank">
                <h3 class="manual-edit__preview-rank-title">
                    <?= $rank['existing_id'] === null ? '+ Nový rejstřík: ' : '~ Rejstřík: ' ?><?= h($rank['name']) ?>
                </h3>
                <?php if($rank['existing_id'] === null && !empty($rank['name_de'])): ?>
                <p class="manual-edit__preview-meta">Německý název: <?= h($rank['name_de']) ?></p>
                <?php endif; ?>
                <?php if(!empty($rank['name_de_changed'])): ?>
                <p class="manual-edit__preview-meta">~ Německý název: <strong><?= h($rank['name_de']) ?></strong></p>
                <?php endif; ?>
                <?php if($rank['existing_id'] === null && !empty($rank['description'])): ?>
                <p class="manual-edit__preview-meta">Popis: <?= h($rank['description']) ?></p>
                <?php endif; ?>
                <?php if(!empty($rank['description_changed'])): ?>
                <p class="manual-edit__preview-meta">~ Popis: <strong><?= h($rank['description']) ?></strong></p>
                <?php endif; ?>
                <ul class="manual-edit__preview-pipes">
                    <?php foreach($rank['pipes'] as $pipe): ?>
                    <li>
                        <?php if($pipe['type'] === 'update'): ?>
                            ~ <?= h($pipe['tone_name']) ?>: <?= number_format($pipe['old_price'],0,',','.') ?> → <strong><?= number_format($pipe['new_price'],0,',','.') ?> Kč</strong>
                        <?php else: ?>
                            + <?= h($pipe['tone_name']) ?>: <strong><?= number_format($pipe['price'],0,',','.') ?> Kč</strong>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
            <?php if(!empty($pendingUpdate['warnings'])): ?>
            <div class="manual-edit__preview-warnings">
                <h3>Upozornění (tyto změny nebudou provedeny):</h3>
                <ul>
                    <?php foreach($pendingUpdate['warnings'] as $w): ?>
                    <li><?= h($w) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <form method="post" style="margin-top: 1.5rem">
            <input type="hidden" name="_method" value="post">
            <input type="hidden" name="_csrfToken" value="<?= $token ?>">
            <?php if(!empty($pendingUpdate['ranks'])): ?>
            <input type="submit" name="apply" class="action-button data-page__upload-button" value="Potvrdit a uložit">
            <?php endif; ?>
            <input type="submit" name="cancelUpdate" class="action-button manual-edit__delete-button" value="Storno">
        </form>
    </div>
    <?php else: ?>

    <form class="data-page__form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrfToken" value="<?= $token ?>">

        <h1 class="data-page__heading">Nahrát data z CSV souboru</h1>
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
            <a href="/admin/data?export=<?= $m['id'] ?>" class="action-button manual-edit__export-button">Exportovat</a>
            <form method="post">
                <input type="hidden" name="_method" value="post">
                <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                <input type="hidden" name="machineId" value="<?= $m['id'] ?>">
                <input type="submit" name="delete" class="action-button  manual-edit__delete-button" value="Smazat stroj">
            </form>
        </div>
        <?php } ?>
    </div>

    <?php if(!empty($machines)): ?>
    <form class="data-page__form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="_method" value="post">
        <input type="hidden" name="_csrfToken" value="<?= $token ?>">

        <h1 class="data-page__heading">Aktualizovat stroj z CSV souboru</h1>
        <p class="data-page__hint">Pokud název rejstříku odpovídá existujícímu, aktualizují se ceny. Nový název rejstříku vytvoří nový rejstřík. Adoptované píšťaly označte v CSV jako <strong>Obsazeno</strong> – rezervované nebo adoptované píšťaly jsou chráněny. Před uložením uvidíte náhled změn.</p>
        <input id="updateFile" class="data-page__file-input" type="file" name="machine" accept=".csv,.xlsx,.xls">
        <div class="data-page__inputs">
            <label for="updateFile" class="data-page__file-input-label">Vybrat CSV soubor</label>
            <div class="input-field">
                <label class="input-label" for="updateMachineId">Stroj</label>
                <select id="updateMachineId" class="input-field__input data-page__text-input" name="updateMachineId" required>
                    <?php foreach($machines as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= h($m['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="input-field__error-text">&nbsp;</p>
            </div>
        </div>
        <input type="submit" class="action-button data-page__upload-button" name="preview" value="Zobrazit náhled">
    </form>

    <div class="manual-edit">
        <h1 class="manual-edit__heading">Správa rejstříků</h1>
        <?php foreach($machines as $m): ?>
        <div class="manual-edit__machine-section">
            <h2 class="manual-edit__text"><?= h($m['name']) ?></h2>
            <?php if(empty($m['ranks'])): ?>
                <p style="margin-left: 1rem; color: #888">Žádné rejstříky.</p>
            <?php else: ?>
            <?php foreach($m['ranks'] as $r): ?>
            <div class="manual-edit__item manual-edit__item--rank">
                <span class="manual-edit__text manual-edit__text--small">
                    <?= h($r['name']) ?>
                    <small>(volné: <?= (int)$r['free_count'] ?>, obsazené: <?= (int)$r['locked_count'] ?>)</small>
                </span>
                <?php if((int)$r['locked_count'] === 0): ?>
                <form method="post" style="display: inline" onsubmit="return confirm('Opravdu smazat rejstřík \'<?= addslashes(h($r['name'])) ?>\'?')">
                    <input type="hidden" name="_method" value="post">
                    <input type="hidden" name="_csrfToken" value="<?= $token ?>">
                    <input type="hidden" name="rankId" value="<?= $r['id'] ?>">
                    <input type="submit" name="deleteRank" class="action-button manual-edit__delete-button" value="Smazat">
                </form>
                <?php else: ?>
                <span style="color: #aaa; font-size: 0.85rem">nelze smazat</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</main>
