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
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class ContactController extends AppController
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

        $this->set("active", "contact");
        
        if(isset($_POST['send'])){
            return $this->sendMessage();
        }

        $this->set("flashCount", parent::printFlush());
        $token = $this->request->getAttribute('csrfToken');
        $_SESSION['token'] = $token;
        $this->set("token", $token);

        try {
            return $this->render("home");
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
    
    public function sendMessage(){
        if(isset($_POST['name']) && trim($_POST['name']) != ""
            && isset($_POST['email']) && trim($_POST['email']) != ""
            && isset($_POST['message']) && trim($_POST['message']) != ""){
       
            $email = new Mailer('default');
            $email->setFrom("adopce@varhanyprokrpole.cz");
            $email->setEmailFormat("html");
            $email->setTo("adopce@varhanyprokrpole.cz");
            $email->setSubject("Zpráva z webu");
            $email->deliver("Zpráva z webu Varhany pro Královo Pole od " . $_POST['name'] . " (" . $_POST['email'] . ")<br><br>" . $_POST['message']);
            
            $_SESSION['errorMessage'][] = "Děkujeme za zprávu.";
            return $this->redirect("/kontakt");
        } else {
            $_SESSION['errorMessage'][] = "Vyplňte, prosím, všechna pole.";
            return $this->redirect("/kontakt");
        }
            
    }
}
