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

use Cake\Controller\Controller;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        parent::initialize();

        session_start();

        $this->loadComponent('Flash');
        //if($this->getRequest("controller")->getParam('controller') != "IFileUploader"){
            $this->loadComponent('FormProtection');
        //}

        /* TODO
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }

    public function printFlush(){
        $flashCount = 0;
        if(isset($_SESSION['successMessage'])){
            foreach($_SESSION['successMessage'] as $m){
                $this->Flash->success($m);
                $flashCount++;
            }
            unset($_SESSION['successMessage']);
        }
        if(isset($_SESSION['errorMessage'])){
            foreach($_SESSION['errorMessage'] as $m){
                $this->Flash->error($m);
                $flashCount++;
            }
            unset($_SESSION['errorMessage']);
        }
        if(isset($_SESSION['warningMessage'])){
            foreach($_SESSION['warningMessage'] as $m){
                $this->Flash->warning($m);
                $flashCount++;
            }
            unset($_SESSION['warningMessage']);
        }
        return $flashCount;
    }
}
