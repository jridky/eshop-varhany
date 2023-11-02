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

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class AuthController extends AppController
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
    public function login(string ...$path): ?Response
    {

        if(isset($_POST['login'])){
            return $this->control();
        }

        $this->set("active", "");

        if(count($path) < 1){
            $path = array("login");
        }

        if($path[0] == "." || $path[0] == ".."){
            throw new NotFoundException();
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

    public function logout(){
        if(isset($_SESSION['access'])){
            session_unset();
            session_destroy();
            session_start();
            $_SESSION = array();
            $_SESSION['successMessage'][] = "Byli jste úspěšně odhlášeni.";
        }
        return $this->redirect("/login/");
    }

    public function control(){
        if(isset($_POST['username']) && $_POST['username'] === Configure::read("WebAccess.username") && isset($_POST['password']) and $_POST['password'] === Configure::read("WebAccess.password")){
            $_SESSION['access'] = true;
            session_regenerate_id();
            return $this->redirect("/admin/");
        }

        $_SESSION['errorMessage'][] = "Chybné heslo.";
        return $this->redirect("/login/");
    }
}
