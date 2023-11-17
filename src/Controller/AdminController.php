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
                $this->showPipes($connection);
                break;
            case "objednavky":
                $this->set("active", "orders");
                if(isset($_POST['confirm'])){
                    if($_SESSION['token'] == $_POST['_csrfToken']){
                        return $this->confirmOrder($connection);
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
                $pipes = $connection->execute("SELECT id, price, state, owner FROM pipe WHERE machine_id = " . $m['id'] . " AND rank_id = " . $r['id'] . " ORDER BY tone_id")->fetchAll("assoc");
                $ranks[$rkey]['pipes'] = $pipes;
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
            "FROM orders, pipe, rank, machine, tone WHERE orders.state = 1 AND orders.pipe_id = pipe.id and pipe.rank_id = rank.id and pipe.machine_id = machine.id " .
            "and pipe.tone_id = tone.id ORDER BY id DESC")->fetchAll("assoc");
        $cancelled = $connection->execute("SELECT orders.*, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe_name, pipe.price as pipe_price " .
            "FROM orders, pipe, rank, machine, tone WHERE orders.state = -1 AND orders.pipe_id = pipe.id and pipe.rank_id = rank.id and pipe.machine_id = machine.id " .
            "and pipe.tone_id = tone.id ORDER BY id DESC")->fetchAll("assoc");

        $this->set("unprocessed", $unprocessed);
        $this->set("processed", $processed);
        $this->set("cancelled", $cancelled);
    }
    
    public function confirmOrder($connection){
        $data = $connection->execute("SELECT p.id, p.price, o.email, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe FROM pipe as p, orders as o, machine, rank, tone WHERE o.id = " . $_POST['order_id'] . " AND p.id = o.pipe_id  and p.rank_id = rank.id and p.machine_id = machine.id and p.tone_id = tone.id")->fetch("assoc");
        
        $connection->execute("UPDATE orders SET state = 1 WHERE id = " . $_POST['order_id']);
        $connection->execute("UPDATE pipe SET state = 2 WHERE id = " . $data['id']);
        
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
        $data = $connection->execute("SELECT p.id, p.price, o.email, CONCAT(machine.name,', ',rank.name,', ',tone.name) as pipe FROM pipe as p, orders as o, machine, rank, tone WHERE o.id = " . $_POST['order_id'] . " AND p.id = o.pipe_id  and p.rank_id = rank.id and p.machine_id = machine.id and p.tone_id = tone.id")->fetch("assoc");
        
        $connection->execute("UPDATE orders SET state = -1, confirmation=0 WHERE id = " . $_POST['order_id']);
        $connection->execute("UPDATE pipe SET state = 0, owner=NULL WHERE id = " . $data['id']);
        
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
