<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;


/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class AdminController extends AppController
{
    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {

        if(!isset($_SESSION['access']) || !$_SESSION['access']){
            $_SESSION['errorMessage'][] = "Nemáte oprávnění pro vstup do interní sekce. Nejprve se přihlaste.";
            return $this->redirect("/login/");
        }

        $this->viewBuilder()->setLayout('internal');

        $connection = ConnectionManager::get('default');

        if(count($path) < 1){
            $path = array("objednavky");
        }

        if($path[0] == "." || $path[0] == ".."){
            throw new NotFoundException();
        }

        switch($path[0]){
            case "data":
                $this->set("active", "data");
                $exportId = $this->request->getQuery('export');
                if($exportId !== null && is_numeric($exportId)){
                    return $this->exportMachineCSV($connection, (int)$exportId);
                } elseif(isset($_POST['load'])){
                    if($_SESSION['token'] == $_POST['_csrfToken'] && $_POST['name'] != ""){
                        return $this->processCSV($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['preview'])){
                    if($_SESSION['token'] == $_POST['_csrfToken'] && is_numeric($_POST['updateMachineId'])){
                        return $this->previewMachineCSV($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['apply'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->applyMachineCSV($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['cancelUpdate'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        unset($_SESSION['pendingUpdate']);
                    }
                } elseif(isset($_POST['deleteRank'])){
                    if($_SESSION['token'] == $_POST['_csrfToken'] && is_numeric($_POST['rankId'])){
                        return $this->deleteRank($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['delete'])){
                    if($_SESSION['token'] == $_POST['_csrfToken'] && is_numeric($_POST['machineId'])){
                        return $this->deleteMachine($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                }
                $this->showData($connection);
                break;
            case "pistaly":
                $this->set("active", "pipes");
                $this->showPipes($connection);
                break;
            case "objednavky":
                $this->set("active", "orders");
                if(isset($_POST['confirm'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->confirmOrder($connection, 1);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['confirm-account'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->confirmOrder($connection, 2);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['confirm-public'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->confirmOrder($connection, 3);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                } elseif(isset($_POST['reject'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->rejectOrder($connection);
                    } else {
                        $_SESSION['errorMessage'][] = "Chyba zpracování";
                    }
                }
                $this->showOrders($connection);
                break;
            default: break;
        }

        $token = $this->request->getAttribute('csrfToken');
        $_SESSION['token'] = $token;
        $this->set("token", $token);

        $this->set("flashCount", parent::printFlush());

        try {
            return $this->render($path[0]);
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }

        return $this->render();
    }

    public function exportMachineCSV($connection, int $machineId){
        $machine = $connection->execute("SELECT id, name FROM machine WHERE id = ?", [$machineId])->fetch("assoc");
        if(!$machine){
            $_SESSION['errorMessage'][] = "Stroj nenalezen.";
            return $this->redirect("/admin/data");
        }

        $tones = $connection->execute("SELECT id, name FROM tone WHERE machine_id = ? ORDER BY id", [$machineId])->fetchAll("assoc");
        $ranks = $connection->execute("SELECT id, name, name_de, description FROM rank WHERE machine_id = ? ORDER BY name, id", [$machineId])->fetchAll("assoc");

        $buf = fopen('php://temp', 'r+');

        $header = ["Rejstřík / Tón", "Německý název", "textový popis"];
        foreach($tones as $tone) $header[] = $tone['name'];
        fputcsv($buf, $header);

        foreach($ranks as $rank){
            $pipes = $connection->execute(
                "SELECT tone_id, price, state FROM pipe WHERE rank_id = ? AND machine_id = ?",
                [$rank['id'], $machineId]
            )->fetchAll("assoc");
            $pipesByTone = array_column($pipes, null, 'tone_id');

            $row = [$rank['name'], $rank['name_de'] ?? '', $rank['description'] ?? ''];
            foreach($tones as $tone){
                if(!isset($pipesByTone[$tone['id']])){
                    $row[] = "";
                } else {
                    $pipe = $pipesByTone[$tone['id']];
                    $row[] = ((int)$pipe['state'] >= 1) ? "Obsazeno" : (int)$pipe['price'];
                }
            }
            fputcsv($buf, $row);
        }

        rewind($buf);
        $csv = stream_get_contents($buf);
        fclose($buf);

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $machine['name']);
        $filename = "export_" . $safeName . "_" . date('Y-m-d') . ".csv";

        return $this->response
            ->withType('text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withStringBody($csv);
    }

    public function previewMachineCSV($connection){
        try{
            if($_FILES['machine']['name'] == "") throw new \Exception("no-file");
            if(strpos($_FILES['machine']['type'], "spreadsheet") === false &&
               strpos($_FILES['machine']['type'], "text/csv") === false &&
               strpos($_FILES['machine']['type'], "excel") === false){
                throw new \Exception("file");
            }

            $machineId = (int)$_POST['updateMachineId'];
            $machine = $connection->execute("SELECT id, name FROM machine WHERE id = ?", [$machineId])->fetch("assoc");
            if(!$machine) throw new \Exception("Stroj nenalezen.");

            $file = IOFactory::load($_FILES['machine']['tmp_name']);
            $data = $file->getActiveSheet()->toArray(NULL, TRUE, FALSE, FALSE);

            $tonesByName = array_column(
                $connection->execute("SELECT id, name FROM tone WHERE machine_id = ?", [$machineId])->fetchAll("assoc"),
                'id', 'name'
            );
            $ranksByName = array_column(
                $connection->execute("SELECT id, name, name_de, description FROM rank WHERE machine_id = ?", [$machineId])->fetchAll("assoc"),
                null, 'name'
            );

            if(count($data[0]) < 3 || trim((string)$data[0][1]) !== 'Německý název'){
                throw new \Exception("CSV musí být v novém formátu: první řádek \"Rejstřík / Tón\", \"Německý název\", \"textový popis\", tóny…");
            }
            $diff = ['machineId' => $machineId, 'machineName' => $machine['name'], 'hasMetaCols' => true, 'ranks' => [], 'warnings' => []];

            $csvTones = [];
            for($i = 3; $i < count($data[0]); $i++){
                $toneName = trim((string)$data[0][$i]);
                if($toneName === '') continue;
                $csvTones[$i] = ['name' => $toneName, 'id' => $tonesByName[$toneName] ?? null];
            }

            for($i = 1; $i < count($data); $i++){
                $rankName = trim((string)$data[$i][0]);
                if($rankName === '') continue;

                $nameDe = trim((string)$data[$i][1]);
                $description = trim((string)$data[$i][2]);
                $existingRank = $ranksByName[$rankName] ?? null;
                $existingRankId = $existingRank ? (int)$existingRank['id'] : null;
                $rankEntry = ['name' => $rankName, 'existing_id' => $existingRankId, 'name_de' => $nameDe, 'description' => $description, 'pipes' => []];
                if($existingRank !== null){
                    $rankEntry['name_de_changed'] = $nameDe !== ($existingRank['name_de'] ?? '');
                    $rankEntry['description_changed'] = $description !== ($existingRank['description'] ?? '');
                }

                $pipesByTone = [];
                if($existingRankId !== null){
                    $pipesByTone = array_column(
                        $connection->execute(
                            "SELECT id, price, state, tone_id FROM pipe WHERE rank_id = ? AND machine_id = ?",
                            [$existingRankId, $machineId]
                        )->fetchAll("assoc"),
                        null, 'tone_id'
                    );
                }

                for($j = 3; $j < count($data[$i]); $j++){
                    if(!isset($csvTones[$j])) continue;
                    $cellValue = trim((string)$data[$i][$j]);
                    if($cellValue === '' || strtolower($cellValue) === 'obsazeno') continue;
                    if(!is_numeric($cellValue)){
                        throw new \Exception("Neplatná hodnota \"$cellValue\" (řádek " . ($i+1) . ", sloupec " . ($j+1) . ")");
                    }

                    $newPrice = (int)$cellValue;
                    $tone = $csvTones[$j];
                    $toneId = $tone['id'];

                    if($toneId !== null && isset($pipesByTone[$toneId])){
                        $pipe = $pipesByTone[$toneId];
                        if((int)$pipe['state'] >= 1){
                            $diff['warnings'][] = "Rejstřík \"$rankName\" / tón \"{$tone['name']}\" – rezervovaná nebo adoptovaná, cena nebyla změněna.";
                        } elseif((int)$pipe['price'] !== $newPrice){
                            $rankEntry['pipes'][] = ['type' => 'update', 'pipe_id' => (int)$pipe['id'], 'tone_name' => $tone['name'], 'old_price' => (int)$pipe['price'], 'new_price' => $newPrice];
                        }
                    } else {
                        $rankEntry['pipes'][] = ['type' => 'create', 'tone_name' => $tone['name'], 'tone_id' => $toneId, 'price' => $newPrice];
                    }
                }

                $hasMetaChange = ($rankEntry['name_de_changed'] ?? false) || ($rankEntry['description_changed'] ?? false);
                if($existingRankId === null || !empty($rankEntry['pipes']) || $hasMetaChange){
                    $diff['ranks'][] = $rankEntry;
                }
            }

            $_SESSION['pendingUpdate'] = json_encode($diff);

        }catch(\Exception $e){
            switch($e->getMessage()){
                case "file":    $_SESSION['errorMessage'][] = "Špatný typ souboru"; break;
                case "no-file": $_SESSION['errorMessage'][] = "Není soubor – nejsou data"; break;
                default:        $_SESSION['errorMessage'][] = "CSV nelze zpracovat: " . $e->getMessage();
            }
        }

        return $this->redirect("/admin/data");
    }

    public function applyMachineCSV($connection){
        if(!isset($_SESSION['pendingUpdate'])){
            $_SESSION['errorMessage'][] = "Žádná čekající aktualizace.";
            return $this->redirect("/admin/data");
        }

        $diff = json_decode($_SESSION['pendingUpdate'], true);
        unset($_SESSION['pendingUpdate']);

        $stats = ['ranks_updated' => 0, 'ranks_created' => 0, 'pipes_updated' => 0, 'pipes_created' => 0];

        try{
            $connection->execute("START TRANSACTION;");

            $machineId = $diff['machineId'];
            $tonesByName = array_column(
                $connection->execute("SELECT id, name FROM tone WHERE machine_id = ?", [$machineId])->fetchAll("assoc"),
                'id', 'name'
            );

            foreach($diff['ranks'] as $rank){
                if($rank['existing_id'] === null){
                    $rankId = $connection->execute(
                        "INSERT INTO rank (machine_id, name, name_de, description) VALUES (?, ?, ?, ?)",
                        [$machineId, $rank['name'], $rank['name_de'] ?? '', $rank['description'] ?? '']
                    )->lastInsertId();
                    $stats['ranks_created']++;
                } else {
                    $rankId = $rank['existing_id'];
                    $stats['ranks_updated']++;
                    if(!empty($diff['hasMetaCols']) && (($rank['name_de_changed'] ?? false) || ($rank['description_changed'] ?? false))){
                        $connection->execute(
                            "UPDATE rank SET name_de = ?, description = ? WHERE id = ?",
                            [$rank['name_de'] ?? '', $rank['description'] ?? '', $rankId]
                        );
                    }
                }

                foreach($rank['pipes'] as $pipe){
                    if($pipe['type'] === 'update'){
                        $connection->execute("UPDATE pipe SET price = ? WHERE id = ?", [$pipe['new_price'], $pipe['pipe_id']]);
                        $stats['pipes_updated']++;
                    } else {
                        $toneId = $pipe['tone_id'];
                        if($toneId === null){
                            if(isset($tonesByName[$pipe['tone_name']])){
                                $toneId = $tonesByName[$pipe['tone_name']];
                            } else {
                                $toneId = $connection->execute("INSERT INTO tone (name, machine_id) VALUES (?, ?)", [$pipe['tone_name'], $machineId])->lastInsertId();
                                $tonesByName[$pipe['tone_name']] = $toneId;
                            }
                        }
                        $connection->execute(
                            "INSERT INTO pipe (rank_id, machine_id, tone_id, price, state) VALUES (?, ?, ?, ?, 0)",
                            [$rankId, $machineId, $toneId, $pipe['price']]
                        );
                        $stats['pipes_created']++;
                    }
                }
            }

            $connection->execute("COMMIT;");
            $_SESSION['successMessage'][] = "Hotovo – rejstříky aktualizovány: {$stats['ranks_updated']}, vytvořeny: {$stats['ranks_created']}; píšťaly aktualizovány: {$stats['pipes_updated']}, vytvořeny: {$stats['pipes_created']}.";
            foreach($diff['warnings'] as $w){
                $_SESSION['warningMessage'][] = $w;
            }

        }catch(\Exception $e){
            try { $connection->execute("ROLLBACK;"); } catch(\Exception $ignored) {}
            $_SESSION['errorMessage'][] = "Chyba při aplikaci změn: " . $e->getMessage();
        }

        return $this->redirect("/admin/data");
    }

    public function deleteRank($connection){
        $rankId = (int)$_POST['rankId'];

        $blocked = $connection->execute("SELECT COUNT(*) as cnt FROM pipe WHERE rank_id = ? AND state > 0", [$rankId])->fetch("assoc");
        if((int)$blocked['cnt'] > 0){
            $_SESSION['errorMessage'][] = "Rejstřík nelze smazat – obsahuje rezervované nebo adoptované píšťaly.";
            return $this->redirect("/admin/data");
        }

        $rank = $connection->execute("SELECT name FROM rank WHERE id = ?", [$rankId])->fetch("assoc");
        $connection->execute("DELETE FROM pipe WHERE rank_id = ?", [$rankId]);
        $connection->execute("DELETE FROM rank WHERE id = ?", [$rankId]);

        $_SESSION['successMessage'][] = "Rejstřík \"" . $rank['name'] . "\" byl smazán.";
        return $this->redirect("/admin/data");
    }

    public function showData($connection){
        $machines = $connection->execute("SELECT id, name FROM machine ORDER BY id")->fetchAll("assoc");

        foreach($machines as $key => $m){
            $ranks = $connection->execute(
                "SELECT r.id, r.name,
                 COALESCE(SUM(CASE WHEN p.state = 0 THEN 1 ELSE 0 END), 0) as free_count,
                 COALESCE(SUM(CASE WHEN p.state > 0 THEN 1 ELSE 0 END), 0) as locked_count
                 FROM rank r LEFT JOIN pipe p ON p.rank_id = r.id AND p.machine_id = r.machine_id
                 WHERE r.machine_id = ?
                 GROUP BY r.id, r.name ORDER BY r.name",
                [$m['id']]
            )->fetchAll("assoc");
            $machines[$key]['ranks'] = $ranks;
        }

        $this->set("machines", $machines);

        if(isset($_SESSION['pendingUpdate'])){
            $this->set("pendingUpdate", json_decode($_SESSION['pendingUpdate'], true));
        }
    }

    public function processCSV($connection){
        try{
            if($_FILES['machine']['name'] != ""){
                if(strpos($_FILES['machine']['type'], "spreadsheet") === false && strpos($_FILES['machine']['type'], "text/csv") === false && strpos($_FILES['machine']['type'], "excel") === false){
                    throw new \Exception("file");
                }

                $check = $connection->execute("SELECT id FROM machine WHERE name = ?", [$_POST['name']])->fetch("assoc");

                if($check != null){
                    $_SESSION['errorMessage'][] = "Tento název stroje již existuje";
                    return $this->redirect("/admin/data");
                }
                $connection->execute("START TRANSACTION;");
                $machineId = $connection->execute("INSERT INTO machine (name) VALUES (?)", [$_POST['name']])->lastInsertId();
                $file = IOFactory::load($_FILES['machine']['tmp_name']);
                $data = $file->getActiveSheet()->toArray(NULL,TRUE,FALSE,FALSE);

                if(count($data[0]) < 3 || trim((string)$data[0][1]) !== 'Německý název'){
                    throw new \Exception("CSV musí být v novém formátu: první řádek \"Rejstřík / Tón\", \"Německý název\", \"textový popis\", tóny…");
                }

                $tunes = array();
                for($i = 3; $i < sizeof($data[0]); $i++){
                    $tuneId = $connection->execute("SELECT id FROM tone WHERE name LIKE BINARY ? AND machine_id = ?", [$data[0][$i], $machineId])->fetch("assoc");
                    if($tuneId == null){
                        $tunes[$i] = $connection->execute("INSERT INTO tone (name, machine_id) VALUES (?, ?)", [$data[0][$i], $machineId])->lastInsertId();
                    } else {
                        $tunes[$i] = $tuneId['id'];
                    }
                }

                for($i = 1; $i < sizeof($data); $i++){
                    if(trim((string)$data[$i][0]) === '') continue;
                    $rankName = $data[$i][0];
                    $nameDe = trim((string)$data[$i][1]);
                    $description = trim((string)$data[$i][2]);
                    $rankId = $connection->execute(
                        "INSERT INTO rank (machine_id, name, name_de, description) VALUES (?, ?, ?, ?)",
                        [$machineId, $rankName, $nameDe, $description]
                    )->lastInsertId();

                    for($j = 3; $j < sizeof($data[$i]); $j++){
                        $price = trim((string)$data[$i][$j]);
                        if ($price === "" || strtolower($price) === 'obsazeno') continue;
                        if (!is_numeric($price)) {
                            throw new \Exception("Neplatná cena \"" . $price . "\" (řádek " . ($i + 1) . ", sloupec " . ($j + 1) . ")");
                        }
                        if (!isset($tunes[$j])) continue;
                        $connection->execute(
                            "INSERT INTO pipe (rank_id, machine_id, tone_id, price, state) VALUES (?, ?, ?, ?, 0)",
                            [$rankId, $machineId, $tunes[$j], (float)$price]
                        );
                    }
                }
                $connection->execute("COMMIT;");
                $_SESSION['successMessage'][] = "CSV úspěšně nahráno";
            } else {
                throw new \Exception("no-file");
            }

        }catch(\Exception $e){
            try { $connection->execute("ROLLBACK;"); } catch(\Exception $ignored) {}
            switch($e->getMessage()){
                case "file": $_SESSION['errorMessage'][] = "Špatný typ souboru";
                            break;
                case "no-file": $_SESSION['errorMessage'][] = "Není soubor - nejsou data";
                            break;
                default: $_SESSION['errorMessage'][] = "CSV nelze zpracovat. Zkontroluj formát soboru." . $e->getMessage();
            }
        }

        return $this->redirect("/admin/data");
    }

    public function deleteMachine($connection){

        $machineId = (int)$_POST['machineId'];
        $connection->execute("DELETE FROM machine WHERE id = ?", [$machineId]);
        $connection->execute("DELETE FROM pipe WHERE machine_id = ?", [$machineId]);
        $connection->execute("DELETE FROM rank WHERE machine_id = ?", [$machineId]);
        $connection->execute("DELETE FROM tone WHERE machine_id = ?", [$machineId]);

        $_SESSION['successMessage'][] = "Celý stroj byl odebrán.";
        return $this->redirect('/admin/data');
    }

    public function showPipes($connection){

        $machines = $connection->execute("SELECT id, name FROM machine order by id")->fetchAll("assoc");

        foreach($machines as $key => $m){
            $tones = $connection->execute("SELECT id, name FROM tone WHERE machine_id = " . $m['id'] . " ORDER BY id")->fetchAll("assoc");
            $machines[$key]['tones'] = $tones;

            $ranks = $connection->execute("SELECT id, name, description FROM rank WHERE machine_id = " . $m['id'] . " ORDER BY name, id")->fetchAll("assoc");

            foreach($ranks as $rkey => $r){
                $pipes = $connection->execute("SELECT id, price, state, owner, tone_id FROM pipe WHERE machine_id = " . $m['id'] . " AND rank_id = " . $r['id'] . " ORDER BY tone_id")->fetchAll("assoc");
                // indexováno podle tone_id, aby chybějící píšťaly neposunuly sloupce
                $ranks[$rkey]['pipes'] = array_column($pipes, null, 'tone_id');
            }

            $machines[$key]['ranks'] = $ranks;
        }

        $this->set("machines", $machines);
    }
    
    public function showOrders($connection){
        $unprocessed = $connection->execute("SELECT orders.*, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe_name, pipe.price as pipe_price " .
            "FROM orders, pipe, rank, machine, tone WHERE orders.state = 0 AND orders.pipe_id = pipe.id and pipe.rank_id = rank.id and pipe.machine_id = machine.id " .
            "and pipe.tone_id = tone.id ORDER BY id DESC")->fetchAll("assoc");
        $processed = $connection->execute("SELECT orders.*, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe_name, pipe.price as pipe_price " .
            "FROM orders, pipe, rank, machine, tone WHERE orders.state > 0 AND orders.pipe_id = pipe.id and pipe.rank_id = rank.id and pipe.machine_id = machine.id " .
            "and pipe.tone_id = tone.id ORDER BY id DESC")->fetchAll("assoc");
        $cancelled = $connection->execute("SELECT orders.*, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe_name, pipe.price as pipe_price " .
            "FROM orders, pipe, rank, machine, tone WHERE orders.state = -1 AND orders.pipe_id = pipe.id and pipe.rank_id = rank.id and pipe.machine_id = machine.id " .
            "and pipe.tone_id = tone.id ORDER BY id DESC")->fetchAll("assoc");

        $this->set("unprocessed", $unprocessed);
        $this->set("processed", $processed);
        $this->set("cancelled", $cancelled);
    }
    
    public function confirmOrder($connection, int $paymentState = 1){
        if(!is_numeric($_POST['order_id'])){
            $_SESSION['errorMessage'][] = "Neplatná objednávka.";
            return $this->redirect("/admin/objednavky");
        }
        $orderId = (int)$_POST['order_id'];
        $data = $connection->execute("SELECT p.id, p.price, o.email, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe FROM pipe as p, orders as o, machine, rank, tone WHERE o.id = ? AND p.id = o.pipe_id and p.rank_id = rank.id and p.machine_id = machine.id and p.tone_id = tone.id", [$orderId])->fetch("assoc");

        $connection->execute("UPDATE orders SET state = ?, updated=CURRENT_TIMESTAMP WHERE id = ?", [$paymentState, $orderId]);
        $connection->execute("UPDATE pipe SET state = 2 WHERE id = ?", [$data['id']]);
        
        $text = '<html lang="en">
                  <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <link rel="preconnect" href="https://fonts.gstatic.com" />
                    <link href="https://fonts.googleapis.com/css2?family=Roboto&amp;display=swap"
                      rel="stylesheet"/>
                    <title>Document</title>
                  </head>
                  <body
                    style="
                      max-width: 800px;
                      min-width: 400px;
                      padding: 3rem;
                      margin: auto;
                      font-family: \'Roboto\', sans-serif;
                      font-size: 1.125rem;
                      border: 1px solid black;
                    ">
                    <div style="margin-bottom: 2rem">
                      <div>
                        <h2 style="margin: 0">Varhany</h2>
                        <h3 style="margin: 0">pro Královo Pole</h3>
                      </div>
                    </div>
                    <main>
                      <h2>Dobrý den,</h2>
                      <p style="margin-top: 4rem; font-weight: 400">
                        děkujeme za Váš příspěvek. Tímto zasíláme potvrzení o připsání částky <strong>' . number_format($data['price'],0,',','.') . ' Kč</strong>
                        na adopci píšťaly <strong>' . $data['pipe'] . '</strong> na náš účet.
                      </p>
                      <p style="margin-top: 4rem; font-weight: 400">
                        S pozdravem,<br />
                        Varhany pro Královo Pole.
                      </p>
                    </main>
                  </body>
                </html>';
        try{
            $email = new Mailer('default');
            $email->setFrom(["adopce@varhanyprokrpole.cz"=>"Varhany pro Královo Pole"]);
            $email->setEmailFormat("html");
            $email->setTo($data['email']);
            $email->setReplyTo("adopce@varhanyprokrpole.cz");
            $email->setBcc("adopce@varhanyprokrpole.cz");
            $email->setSubject("Potvrzení příspěvku");
            $email->deliver($text);    
            $_SESSION['successMessage'][] = "Objednávka byla potvrzena.";
        }catch(\Exception $e){
            $_SESSION['successMessage'][] = "Objednávka byla potvrzena. Email nemohl být odeslán na adresu (" . $data['email'] . ")";
        }
        return $this->redirect("/admin/objednavky");
    }
    
    public function rejectOrder($connection){
        if(!is_numeric($_POST['order_id'])){
            $_SESSION['errorMessage'][] = "Neplatná objednávka.";
            return $this->redirect("/admin/objednavky");
        }
        $orderId = (int)$_POST['order_id'];
        $data = $connection->execute("SELECT p.id, p.price, o.email, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe FROM pipe as p, orders as o, machine, rank, tone WHERE o.id = ? AND p.id = o.pipe_id and p.rank_id = rank.id and p.machine_id = machine.id and p.tone_id = tone.id", [$orderId])->fetch("assoc");

        $connection->execute("UPDATE orders SET state = -1, confirmation=0, updated=CURRENT_TIMESTAMP WHERE id = ?", [$orderId]);
        $connection->execute("UPDATE pipe SET state = 0, owner=NULL WHERE id = ?", [$data['id']]);
        
        $text = '<html lang="en">
                  <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <link rel="preconnect" href="https://fonts.gstatic.com" />
                    <link href="https://fonts.googleapis.com/css2?family=Roboto&amp;display=swap"
                      rel="stylesheet"/>
                    <title>Document</title>
                  </head>
                  <body
                    style="
                      max-width: 800px;
                      min-width: 400px;
                      padding: 3rem;
                      margin: auto;
                      font-family: \'Roboto\', sans-serif;
                      font-size: 1.125rem;
                      border: 1px solid black;
                    ">
                    <div style="margin-bottom: 2rem">
                      <div>
                        <h2 style="margin: 0">Varhany</h2>
                        <h3 style="margin: 0">pro Královo Pole</h3>
                      </div>
                    </div>
                    <main>
                      <h2>Dobrý den,</h2>
                      <p style="margin-top: 4rem; font-weight: 400">
                        oznamujeme Vám, že Vaše rezervace píšťaly <strong>' . $data['pipe'] .  '</strong> ve výši <strong>' . number_format($data['price'],0,',','.') . ' Kč</strong>
                        byla zrušena. Pokud máte stále zájem o adopci píšťaly, proveďte rezervaci znovu.
                      </p>
                      <p style="margin-top: 4rem; font-weight: 400">
                        S pozdravem,<br />
                        Varhany pro Královo Pole.
                      </p>
                    </main>
                  </body>
                </html>';
        try{
            $email = new Mailer('default');
            $email->setFrom(["adopce@varhanyprokrpole.cz"=>"Varhany pro Královo Pole"]);
            $email->setEmailFormat("html");
            $email->setTo($data['email']);
            $email->setReplyTo("adopce@varhanyprokrpole.cz");
            $email->setBcc("adopce@varhanyprokrpole.cz");
            $email->setSubject("Zrušení rezervace");
            $email->deliver($text);    
            $_SESSION['successMessage'][] = "Objednávka byla zrušena.";
        }catch(\Exception $e){
            $_SESSION['successMessage'][] = "Objednávka byla zrušena. Email nemohl být odeslán na adresu (" . $data['email'] . ")";
        }
        return $this->redirect("/admin/objednavky"); 
    }
}
