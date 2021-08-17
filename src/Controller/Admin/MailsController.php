<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Mailer\Email;

class MailsController extends AppController
{
    private $_fullbaseUrl = null;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Mail');

        $this->_fullbaseUrl = Router::fullbaseUrl();
    }


    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) && !$this->Authentication->getIdentity()->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function suppliers() {

        $user = $this->Authentication->getIdentity();
        if(isset($user->organization))  
            $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if ($this->request->is('post')) {

            $name = '';
            $to = 'francesco.actis@gmail.com';
            $subject = $this->request->getData('mail_subject');
            $body = $this->request->getData('mail_body');
            
            $email = $this->getMailSystem($user);
            
            $email->setViewVars(['body_header' => sprintf(Configure::read('Mail.body_header'), $name)])
                ->setTo($to)
                ->setSubject($subject);

            try {
                $email->send($body);
            } catch (Exception $e) {
                debug($e);
            }                
                
            $this->Flash->success(__('send mail to ').$to);

        }

        $suppliersTable = TableRegistry::get('Suppliers'); 
       
        $where = []; // ['Suppliers.stato' => 'Y'];
        $suppliers = [];
        $results = $suppliersTable->find()
                                ->where($where)
                                ->order(['Suppliers.name' => 'asc'])
                                ->all(); 
        foreach($results as $result) {
            // debug($fullbaseUrl.'/site/produttore/'.$result->slug);
            $suppliers[$result->id] = $this->_fullbaseUrl.'/site/produttore/'.$result->slug;
        }

        $this->set(compact('suppliers'));

        $this->set('fullbaseUrl', $this->_fullbaseUrl);
    }  
}
