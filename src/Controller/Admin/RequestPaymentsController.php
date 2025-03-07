<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class RequestPaymentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        if(!$this->_user->acl['isRoot'] && !$this->_user->acl['isTesoriere']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }


    /*
     * invio mail per chi e' in stato SOLLECITO
     */
    public function mails($id = null)
    {
        $request_payment = $this->RequestPayments->find()
                                ->contain(['SummaryPayments' => [
                                    'Users',
                                    'conditions' => [
                                        'SummaryPayments.organization_id' => $this->_organization->id,
                                        'SummaryPayments.stato IN' => ['SOLLECITO1', 'SOLLECITO2']]]])
                                ->where(['RequestPayments.organization_id' => $this->_organization->id, 'RequestPayments.id' => $id])
                                ->first();

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];

        $mail_subject = $this->_organization->name." - sollecito pagamento per la richiesta num°".$request_payment->num;

        $mail_body_pre = "Salve Mario Rossi,\r\nal momento non è ancora pervenuto il tuo pagamento relativo alla richiesta n°".$request_payment->num." inviata il ".$request_payment->data_send->format('d/m/Y').", ";
        $mail_body = "utile per pagare i fornitori in tempo con gli impegni presi.\r\nSe hai già provveduto al pagamento inviaci una copia del pagamento effettuato così che possa inserirlo fra i saldati.";
        $mail_body_post = "\r\nAl momento risulta da saldare 99,01 €.\r\nPer maggior dettagli Collegati al sito ".$portalgas_fe_url." e, dopo aver fatto la login, scarica il documento per effettuare il pagamento.\r\n\r\nSe effettui il pagamento tramite bonifico indica come causale: Richiesta num ".$request_payment->num." di Mario Rossi\r\nGrazie.";

        $this->set(compact('request_payment', 'mail_subject', 'mail_body_pre', 'mail_body', 'mail_body_post'));
    }
}
