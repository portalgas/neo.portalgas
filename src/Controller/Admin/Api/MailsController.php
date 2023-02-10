<?php
namespace App\Controller\Admin\Api;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Traits;

class MailsController extends ApiAppController
{
    use Traits\MailTrait;

    public function initialize() {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }
  
    /*
     * richiamata dal modal Order::add per richiedere una nuova consegna
     */
    public function requestDeliveryNew() {
        
        $debug = false;
        $results = [];
        $datas = [];
        $datas['mail_body'] = $this->request->getData('mail_body');

        $this->mailInit("Richiesta di apertura di una nuova consegna", 'request_delivery_new');

        /*
         * destinatari mail, utenti con ruolo "manager consegne"
         */
        $userUsergroupMapTable = TableRegistry::get('UserUsergroupMap');
        $groups = [];
        $groups[] = Configure::read('group_id_manager_delivery');
        $where = [];
        $where['Users'] = ['Users.email !=' => ''];
        $userManagerDeliveries = $userUsergroupMapTable->getUsersByGroups($this->_user, $this->_organization->id, $groups, $where);
        if($userManagerDeliveries->count()>0)
        foreach($userManagerDeliveries as $userManagerDelivery) {

            $datas['user'] = $userManagerDelivery->user;
            $this->mailSetViewVars(['datas' => $datas]);

            $mail = trim($userManagerDelivery->user->email);
            $mail = 'francesco.actis@gmail.com';

            $resultMail = $this->mailSend($mail); 
            if($resultMail['esito']) 
                $results['mails']['OK'][] = $resultMail['mail'];
            else
                $results['mails']['OK'][] = $resultMail['mail'];            
        }

        $results['code'] = 200;
        return $this->_response($results); 
    } 
}