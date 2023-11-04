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
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;


/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class AdoptionController extends AppController
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

        $this->set("active", "adoption");
        
        $connection = ConnectionManager::get('default');        
        
        $this->showPipes($connection);
        
        $token = $this->request->getAttribute('csrfToken');
        $_SESSION['token'] = $token;
        $this->set("token", $token);
        
        $this->set("flashCount", parent::printFlush());


        try {
            return $this->render("home");
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }

        return $this->render();
    
    }
    
    public function order(string ...$path): ?Response
    {
        $this->set("active", "adoption");
        
        $connection = ConnectionManager::get('default');     

        $this->set("flashCount", parent::printFlush());
                
        if(isset($_SESSION['desiredPipe']) || (isset($_POST['pipe']) && is_numeric($_POST['pipe']))){
            $this->showOrdered($connection);
        } else if(isset($_POST['pipe_id']) && is_numeric($_POST['pipe_id'])){
            return $this->processOrder($connection);
        } else {
            die(var_dump($_POST));
            return $this->redirect("/adopce");
        }
       
        $token = $this->request->getAttribute('csrfToken');
        $_SESSION['token'] = $token;
        $this->set("token", $token);

        try {
            return $this->render("order");
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }

        return $this->render();
    }
    
    public function confirmation(string ...$path): ?Response
    {
        $this->set("active", "adoption");
        
        $connection = ConnectionManager::get('default');        
        
        $this->showConfirmation($connection);
       
        $token = $this->request->getAttribute('csrfToken');
        $_SESSION['token'] = $token;
        $this->set("token", $token);

        try {
            return $this->render("confirmation");
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }

        return $this->render();
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
    
    public function showOrdered($connection){

        $pipe_id = -1;
        if(!isset($_POST['pipe'])){
            $pipe_id = $_SESSION['desiredPipe'];
        } else {
            $pipe_id = $_POST['pipe'];
        }
        $pipe = $connection->execute("SELECT r.name as rank_name, p.id, price, state, t.name as tone_name FROM pipe as p, rank as r, tone as t WHERE p.id = " . $pipe_id . " AND p.rank_id = r.id AND p.tone_id = t.id")
            ->fetch("assoc");
            
            var_dump($pipe);

        if($pipe['state'] == 0){
            $this->set("pipe", $pipe);
        } else {
            $_SESSION['errorMessage'][] = "Zvolená píšťala je již adoptována. Vyberte si, prosím, jinou.";
            return $this->redirect("/adopce");
        }
    }
    
    public function processOrder($connection){
    
        $state = $connection->execute("SELECT state FROM pipe WHERE id = " . $_POST['pipe_id'])->fetch("assoc");      
           
        
        if($state['state'] != 0){
            $_SESSION['errorMessage'][] = "Zvolenou píšťalu si právě někdo adoptoval. Vyberte si, prosím, jinou.";
            return $this->redirect("/adopce");
        } else {
            if(!isset($_POST['consent'])){
                $_SESSION['errorMessage'][] = "Je nutné udělit souhlas se zpracováním osobních údajů.";
                $_SESSION['desiredPipe'] = $_POST['pipe_id'];
                return $this->redirect("/adopce/objednavka");
            } else {
                if(isset($_POST['firstName'])){
                    if(isset($_POST['firstName']) && trim($_POST['firstName']) != ""
                         && isset($_POST['lastName']) && trim($_POST['lastName']) != ""
                         && isset($_POST['email']) && trim($_POST['email']) != ""){
                         
                         $orderId = $connection->execute("INSERT INTO orders (first_name, last_name, email, pipe_id) VALUES (" .
                            parent::sanity($_POST['firstName']) . "," . parent::sanity($_POST['lastName']) . "," . parent::sanity($_POST['email']) . "," . $_POST['pipe_id'] . ")")->lastInsertId();
                            
                         $connection->execute("UPDATE pipe SET state = 1 WHERE id = " . $_POST['pipe_id']);
                         
//                         $this->sendConfirmation($connection, $orderId);
                         
                         if(isset($_POST['confirmation'])){
                            if(isset($_POST['country']) && trim($_POST['country']) != ""
                                && isset($_POST['city']) && trim($_POST['city']) != ""
                                && isset($_POST['postcode']) && trim($_POST['postcode']) != ""
                                && isset($_POST['address']) && trim($_POST['address']) != ""
                                && isset($_POST['birthdate']) && trim($_POST['birthdate']) != ""){
                                
                                $connection->execute("UPDATE orders SET country=" . parent::sanity($_POST['country']) . ", city=" . parent::sanity($_POST['city']) . ", zip="
                                        . parent::sanity($_POST['postcode']) . ", address=" . parent::sanity($_POST['address']) . ", birth_date=" . parent::sanity($_POST['birthdate']) . ","
                                        . " confirmation=1 WHERE id = " . $orderId);
                            } else {
                                $_SESSION['errorMessage'][] = "Nevyplnili jste všechna povinná pole pro odeslání potvrzení o daru. Kontaktujte nás, prosím, prostřednictvím kontaktního formuláře.";
                            }
                         }
                         
                         if(isset($_POST['public'])){
                            if(isset($_POST['owner']) && trim($_POST['owner']) != ""){
                                $connection->execute("UPDATE orders SET public=1 WHERE id = " . $orderId);
                                $connection->execute("UPDATE pipe SET owner=" . parent::sanity($_POST['owner']) . " WHERE id = " . $_POST['pipe_id']);
                            } else {
                                $_SESSION['errorMessage'][] = "Nevyplnili jste všechna povinná pole pro uveřejnění v seznamu dárců. Kontaktujte nás, prosím, prostřednictvím kontaktního formuláře.";
                            }
                         }
                         
                         $_SESSION['lastOrder'] = $orderId;
                         
                         return $this->redirect("/adopce/potvrzeni");
                
                    } else {
                        $_SESSION['errorMessage'][] = "Nebyla vyplněna všechna povinná pole.";
                        $_SESSION['desiredPipe'] = $_POST['pipe_id'];
                        return $this->redirect("/adopce/objednavka");
                    }
                } else {
                    if(isset($_POST['companyName']) && trim($_POST['companyName']) != ""
                         && isset($_POST['email']) && trim($_POST['email']) != ""){
                         
                         $orderId = $connection->execute("INSERT INTO orders (company_name, email, pipe_id) VALUES (" .
                            parent::sanity($_POST['companyName']) . "," . parent::sanity($_POST['email']) . "," . $_POST['pipe_id'] . ")")->lastInsertId();
                            
                         $connection->execute("UPDATE pipe SET state = 1 WHERE id = " . $_POST['pipe_id']);
                         
//                         $this->sendConfirmation($connection, $orderId);
                         
                         if(isset($_POST['confirmation'])){
                            if(isset($_POST['country']) && trim($_POST['country']) != ""
                                && isset($_POST['city']) && trim($_POST['city']) != ""
                                && isset($_POST['postcode']) && trim($_POST['postcode']) != ""
                                && isset($_POST['address']) && trim($_POST['address']) != ""
                                && isset($_POST['ico']) && trim($_POST['ico']) != ""){
                                
                                $connection->execute("UPDATE orders SET country=" . parent::sanity($_POST['country']) . ", city=" . parent::sanity($_POST['city']) . ", zip="
                                        . parent::sanity($_POST['postcode']) . ", address=" . parent::sanity($_POST['address']) . ", ico=" . parent::sanity($_POST['ico']) . ","
                                        . " confirmation=1 WHERE id = " . $orderId);
                            } else {
                                $_SESSION['errorMessage'][] = "Nevyplnili jste všechna povinná pole pro odeslání potvrzení o daru. Kontaktujte nás, prosím, prostřednictvím kontaktního formuláře.";
                            }
                         }
                         
                         if(isset($_POST['public'])){
                            if(isset($_POST['owner']) && trim($_POST['owner']) != ""){
                                $connection->execute("UPDATE orders SET public=1 WHERE id = " . $orderId);
                                $connection->execute("UPDATE pipe SET owner=" . parent::sanity($_POST['owner']) . " WHERE id = " . $_POST['pipe_id']);
                            } else {
                                $_SESSION['errorMessage'][] = "Nevyplnili jste všechna povinná pole pro uveřejnění v seznamu dárců. Kontaktujte nás, prosím, prostřednictvím kontaktního formuláře.";
                            }
                         }
                         
                         $_SESSION['lastOrder'] = $orderId;
                         
                         return $this->redirect("/adopce/potvrzeni");
                
                    } else {
                        $_SESSION['errorMessage'][] = "Nebyla vyplněna všechna povinná pole.";
                        $_SESSION['desiredPipe'] = $_POST['pipe_id'];
                        return $this->redirect("/adopce/objednavka");
                    }
                }
            }
        }
    }
    
    public function showConfirmation($connection){
        if(isset($_SESSION['lastOrder'])){
            $data = $connection->execute("SELECT o.id, p.price FROM pipe as p, orders as o WHERE o.id = " . $_SESSION['lastOrder'] . " AND p.id = o.pipe_id")->fetch("assoc");
            unset($_SESSION['lastOrder']);
            $this->set("data", $data);
        } else {
            return $this->redirect("/adopce");
        }        
    }
    
    public function sendConfirmation($connection, $orderID){
    
        $data = $connection->execute("SELECT p.price FROM pipe as p, orders as o WHERE o.id = " . $orderID . " AND p.id = o.pipe_id")->fetch("assoc");
    
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
                        děkujeme Vám za zájem o adopci píšťaly varhan pro Královo Pole přes
                        náš portál. Níže naleznete údaje pro dokončení platby bankovním převodem.
                      </p>
                      <table style="margin-top: 4rem; margin-left: 20px">
                        <tbody>
                          <tr>
                            <th style="text-align: right; padding-right: 20px">
                              Bankovní účet:
                            </th>
                            <td>249610162/0300</td>
                          </tr>
                          <tr>
                            <th style="text-align: right; padding-right: 20px">
                              Variabilní symbol:
                            </th>
                            <td>' . $orderID . '</td>
                          </tr>
                          <tr>
                            <th style="text-align: right; padding-right: 20px">
                              Částka:
                            </th>
                            <td>' . number_format($data['price'],0,',','.') . ' Kč</td>
                          </tr>
                        </tbody>
                      </table>
                      <p style="margin-top: 4rem; font-weight: 400">
                        Po kontrole připsání částky na náš účet Vám bude zaslán
                        potvrzující email.
                      </p>
                      <p style="margin-top: 4rem; font-weight: 400">
                        S pozdravem,<br />
                        Varhany pro Královo Pole.
                      </p>
                    </main>
                  </body>
                </html>';

        $email = new Mailer('default');
        $email->setFrom(["adopce@varhanyprokrpole.cz"=>"Varhany pro Královo Pole"]);
        $email->setEmailFormat("html");
        $email->setTo($_POST['email']);
        $email->setReplyTo("adopce@varhanyprokrpole.cz");
        $email->setBcc("adopce@varhanyprokrpole.cz");
        $email->setSubject("Platební údaje");
        $email->deliver($text);   
    }
}
