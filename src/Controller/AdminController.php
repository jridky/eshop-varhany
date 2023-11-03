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
            $path = array("data");
        }

        if($path[0] == "." || $path[0] == ".."){
            throw new NotFoundException();
        }

        switch($path[0]){
            case "data":
                $this->set("active", "data");
                if(isset($_POST['load'])){
                    if($_SESSION['token'] == $_POST['_csrfToken'] && $_POST['name'] != ""){
                        return $this->processCSV($connection);
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
                if(count($path) > 1 && is_numeric($path[1])){
                    return $this->editUpozorneni($connection, $path[1]);
                } elseif( count($path) > 3 && $path[1] == "smazat" && is_numeric($path[2]) && $_SESSION['token'] == $path[3]){
                    return $this->deleteUpozorneni($connection, $path[2]);
                }
                $this->showPipes($connection);
                break;
            case "objednavky":

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

    public function showData($connection){
        $machines = $connection->execute("SELECT id, name FROM machine ORDER BY id")->fetchAll("assoc");

        $this->set("machines", $machines);
    }

    public function processCSV($connection){
        try{
            if($_FILES['machine']['name'] != ""){
                if(strpos($_FILES['machine']['type'], "spreadsheet") === false && strpos($_FILES['machine']['type'], "text/csv") === false && strpos($_FILES['machine']['type'], "excel") === false){
                    throw new \Exception("file");
                }

                $check = $connection->execute("SELECT id FROM machine WHERE name like('" . $_POST['name'] . "')")->fetch("assoc");

                if($check != null){
                    $_SESSION['errorMessage'][] = "Tento název stroje již existuje";
                    return $this->redirect("/admin/data");
                }
                $connection->execute("START TRANSACTION;");
                $machineId = $connection->execute("INSERT INTO machine (name) VALUES ('" . $_POST['name'] . "')")->lastInsertId();
                $file = IOFactory::load($_FILES['machine']['tmp_name']);
                $data = $file->getActiveSheet()->toArray(NULL,TRUE,FALSE,FALSE);

                $tunes = array();
                //process tunes
                for($i = 1; $i < sizeof($data[0]); $i++){
                    $tuneId = $connection->execute('SELECT id FROM tone WHERE name LIKE BINARY ("' . $data[0][$i] . '") and machine_id = ' . $machineId)->fetch("assoc");
                    if($tuneId == null){
                        $tunes[$i] = $connection->execute('INSERT INTO tone (name, machine_id) VALUES ("' . $data[0][$i] . '", ' . $machineId . ')')->lastInsertId();
                    } else {
                        $tunes[$i] = $tuneId['id'];
                    }
                }

                for($i = 1; $i < sizeof($data); $i++){
                    $rankId = 0;
                    for($j = 0; $j < sizeof($data[$i]); $j++){
                        if($j == 0){
                            $rankId = $connection->execute("INSERT INTO rank (machine_id, name) VALUES (" . $machineId . ", '" . $data[$i][$j] . "')")->lastInsertId();
                        } else {
                            $connection->execute("INSERT INTO pipe (rank_id, machine_id, tone_id, price, state) VALUES (" . $rankId . ", " . $machineId . ", " . $tunes[$j] . ", " . $data[$i][$j] . ", 0)");
                        }
                    } 
                }
                $connection->execute("COMMIT;");
                $_SESSION['successMessage'][] = "CSV úspěšně nahráno";
            } else {
                throw new \Exception("no-file");
            }

        }catch(\Exception $e){
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

        $connection->execute("DELETE FROM machine WHERE id = " . $_POST['machineId']);
        $connection->execute("DELETE FROM pipe WHERE machine_id = " . $_POST['machineId']);
        $connection->execute("DELETE FROM rank WHERE machine_id = " . $_POST['machineId']);
        $connection->execute("DELETE FROM tone WHERE machine_id = " . $_POST['machineId']);

        $_SESSION['successMessage'][] = "Celý stroj byl odebrán.";
        return $this->redirect('/admin/data');
    }

    public function showPipes($connection){

        $machines = $connection->execute("SELECT id, name FROM machine order by id")->fetchAll("assoc");

        foreach($machines as $key => $m){
            $tones = $connection->execute("SELECT id, name FROM tone WHERE machine_id = " . $m['id'] . " ORDER BY id")->fetchAll("assoc");
            $machines[$key]['tones'] = $tones;

            $ranks = $connection->execute("SELECT id, name FROM rank WHERE machine_id = " . $m['id'] . " ORDER BY name, id")->fetchAll("assoc");

            foreach($ranks as $rkey => $r){
                $pipes = $connection->execute("SELECT id, FORMAT(price,0,'de_DE') as price, state, owner FROM pipe WHERE machine_id = " . $m['id'] . " AND rank_id = " . $r['id'] . " ORDER BY tone_id")->fetchAll("assoc");
                $ranks[$rkey]['pipes'] = $pipes;
            }

            $machines[$key]['ranks'] = $ranks;
        }

        $this->set("machines", $machines);
    }
}
