<?php
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
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Traits;
use Authentication\IdentityInterface;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    protected $user = null;
    
    public $helpers = [
        'Modal' => [
            'className' => 'Bootstrap.Modal'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', ['enableBeforeRedirect' => false]);
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security'); 
        $this->loadComponent('Authentication.Authentication', Configure::read('Auth'));
        $this->loadComponent('Authorization.Authorization', [
                        'skipAuthorization' => ['login']
                    ]); // definito come middleware in src/Application.php
        // utilizza Auth $this->loadComponent('CakeImpersonate.Impersonate'); 
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event); 

        if(!empty($this->request->prefix) && $this->request->prefix!='admin/api') {
            $result = $this->Authentication->getResult();
            // debug($result); 
            if ($result->isValid()) {
                $this->user = $this->Authentication->getIdentity();
                // debug($identity);
            }
            else {
                debug($result->getStatus());
                debug($result->getErrors());
                // exit;
            } 
        } // end if($this->request->prefix!='admin/api') 
    }
    
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        /*
         * cosi' faccio l'ovveride degli Elements 
         * con json/pdf devo fare l'ovveride con $this->viewBuilder()->setClassName('Json/CakePdf.Pdf');
         */
        $this->viewBuilder()->setClassName('AdminLTE.AdminLTE'); 
        $this->viewBuilder()->setTheme('AdminLTE'); 
    }
}