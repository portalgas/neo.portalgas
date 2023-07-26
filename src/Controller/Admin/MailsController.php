<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;

class MailsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Mail');
        $this->loadComponent('Supplier');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        

        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function suppliers() {
   
        $mail_test = 'francesco.actis@gmail.com';
        $mail_subject = "Nuova pagina produttori";
        $mail_body = "Abbiamo ridisegnato la pagina dei produttori per renderla più fruibile ai G.A.S.<br />
Questo è l'indirizzo relativo alla tua pagina <a href='%s' target='_blank'>%s</a>: se i dati non fossero corretti scrivici all'indirizzo mail 
<a href='mailto:".Configure::read('SOC.mail-contatti')."'>".Configure::read('SOC.mail-contatti')."</a><br />
Grazie per la collaborazione.<br />
Francesco & Marco
        ";
        $this->set(compact('mail_subject', 'mail_body', 'mail_test')); 

        /*
         * in MailComponent scrive log su debug.log 
         */
        $config = Configure::read('Config');
        ($config['mail.send']===false) ? $mail_send_label = "Modalità invio mail DEBUG": $mail_send_label = "Modalità invio mail PRODUZIONE";
        $this->set('mail_send_label', $mail_send_label);

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
        // $listOrganizationsProdGas[0] = __('ALL');
        foreach($organizationsProdGas as $result) {
            $listOrganizationsProdGas[$result->supplier->id] = $this->Supplier->getSlug($result->supplier);
        }
        // debug($listOrganizationsProdGas);
        $this->set(compact('listOrganizationsProdGas'));
       
        /*
         * produttori 
         */        
        $suppliersTable = TableRegistry::get('Suppliers'); 
       
        $where = ['Suppliers.stato IN' => ['Y', 'T']]; // ENUM('Y', 'N', 'T', 'PG')
        $results = $suppliersTable->find()
                                ->where($where)
                                ->order(['Suppliers.name' => 'asc'])
                                ->all(); 
        // $listSuppliers[0] = __('ALL');                                
        foreach($results as $result) {
            $listSuppliers[$result->id] = $this->Supplier->getSlug($result);
        }
        $this->set(compact('listSuppliers'));

        /* 
         * log file mail.log
         */
        $is_logs = ['Y' => 'Si', 'N' => 'No'];
        $is_log = $this->request->getData('is_log');
        if(empty($is_log)) $is_log = 'Y';
        $this->set(compact('is_logs', 'is_log'));
        ($is_log=='Y') ? $debug = true: $debug = false;

        if ($this->request->is('post')) {

            // debug($this->request->getData());
            
            $mail_test = $this->request->getData('mail_test');
            if(empty($mail_test)) {
                $organization_prod_gas_id = $this->request->getData('organization_prod_gas_id');
                $supplier_id = $this->request->getData('supplier_id');

                $ids = [];
                if(!empty($organization_prod_gas_id))
                    $ids = $organization_prod_gas_id;
                else
                if(!empty($supplier_id))
                    $ids = $supplier_id;

                if(empty($ids)) {
                    $this->Flash->error('Seleziona i produttori ai quali inviare la mail');                
                    return $this->redirect(['action' => 'suppliers']);
                }
            }
            else {
                $ids = [21]; // allegro-natura: produttore per avere i dati (ex slug)
            }  // end if(empty($mail_test))  

            $mail_subject = $this->request->getData('mail_subject');
            $mail_body = $this->request->getData('mail_body');
            if(empty($mail_subject) || empty($mail_body)) {
                $this->Flash->error('Soggetto e testo obbligatorio');                
                return $this->redirect(['action' => 'suppliers']);
            }

            /*
             * estraggo produttori
             */
            $where = ['Suppliers.id IN' => $ids];
            $results = $suppliersTable->find()
                                    ->where($where)
                                    ->all(); 
            // debug($where);

            $msg_mails = '';
            foreach($results as $result) {

                // debug($result);

                if(empty($mail_test)) 
                    $mails = trim($result['mail']);
                else
                    $mails = $mail_test;

                $slug = $this->Supplier->getSlug($result);

                if(empty($mails)) {
                    Log::error('ERROR mail EMPTY!', ['scope' => ['mail']]);
                    Log::error($result, ['scope' => ['mail']]);
                    Log::error('ERROR ----------------------!', ['scope' => ['mail']]);
                }
                else
                if(empty($slug)) {
                    Log::error('ERROR slug EMPTY!', ['scope' => ['mail']]);
                    Log::error($result, ['scope' => ['mail']]);
                    Log::error('ERROR ----------------------!', ['scope' => ['mail']]);
                }
                else {
                    $options = [];
                    // $options['name'] = $name;                    
                    $mail_body_finaly = sprintf($mail_body, $slug, $slug);

                    $msg_mails .= "<br />".$mails;
      
                    $this->Mail->send($user, $mails, $mail_subject, $mail_body_finaly, $options, $debug);
                }
            } // foreach($results as $result)
                
            if(!empty($msg_mails))
                $this->Flash->success(__('send mail to ').$msg_mails, ['escape' => false]);
            else
                $this->Flash->error('Nessuna mail inviata, controllare mail.log', ['escape' => false]);
        } // end POST  
    }  
}