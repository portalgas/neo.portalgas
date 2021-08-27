<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;

class MailsController extends AppController
{
    private $_fullbaseUrl = null;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Mail');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) && !$this->Authentication->getIdentity()->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $this->_fullbaseUrl = Router::fullbaseUrl();
    }

    public function suppliers() {
        
        $debug=false;

        $organizationsProdGas = [];
        $listOrganizationsProdGas = [];
        $listSuppliers = [];

        $user = $this->Authentication->getIdentity();
        if(isset($user->organization))  
            $organization_id = $user->organization->id; // gas scelto
        // debug($user);


        /*
         * produttori che gestiscono il listino
         */
        $organizationsProdGasTable = TableRegistry::get('OrganizationsProdGas');
            
        $where = [];
        $where['Organizations'] = ['OrganizationsProdGas.stato' => 'Y']; 
        $organizationsProdGas = $organizationsProdGasTable->gets($where);
        foreach($results as $result) {
            // debug($fullbaseUrl.'/site/produttore/'.$result->slug);
            $listOrganizationsProdGas[$result->supplier->id] = $this->_fullbaseUrl.'/site/produttore/'.$result->supplier->slug;
        }
        // debug($listOrganizationsProdGas);
        $this->set(compact('listOrganizationsProdGas'));
       
        /*
         * produttori 
         */        
        $suppliersTable = TableRegistry::get('Suppliers'); 
       
        $where = []; // ['Suppliers.stato' => 'Y'];
        $suppliers = [];
        $results = $suppliersTable->find()
                                ->where($where)
                                ->order(['Suppliers.name' => 'asc'])
                                ->all(); 
        foreach($results as $result) {
            // debug($fullbaseUrl.'/site/produttore/'.$result->slug);
            $listSuppliers[$result->id] = $this->_fullbaseUrl.'/site/produttore/'.$result->slug;
        }
        $this->set(compact('listSuppliers'));

        if ($this->request->is('post')) {

                    $name = '';
                    $to = 'francesco.actis@gmail.com';
                    $mail_subject = $this->request->getData('mail_subject');
                    $mail_body = $this->request->getData('mail_body');
                    
                    $options = [];
                    $options['name'] = $name;
                    $email = $this->Mail->getMailSystem($user, $options);
                    
                    $email->setTo($to)
                          ->setSubject($mail_subject);
        
                    $this->Mail->send($email, $mails, $body_mail, $debug);

            /*
            foreach($organizationsProdGas as $organizationsProdGa) {
                debug($organizationsProdGa);

                $mail = $organizationsProdGa->supplier->mail;
                if(!empty($mail)) {
                    
                    $name = '';
                    $to = 'francesco.actis@gmail.com';
                    $subject = $this->request->getData('mail_subject');
                    $body = $this->request->getData('mail_body');
                    
                    $options = [];
                    $options['name'] = $name;
                    $email = $this->Mail->getMailSystem($user, $options);
                    
                    $email->setTo($to)
                          ->setSubject($subject);
        
                    try {
                        $email->send($body);
                    } catch (Exception $e) {
                        debug($e);
                    }
                }
            } // foreach
            */
            exit;
                
            $this->Flash->success(__('send mail to ').$to);
        } // end POST

        $this->set('fullbaseUrl', $this->_fullbaseUrl);
    }  
}
