<?php
namespace App\Controller\Admin\Api;

use Cake\Event\Event;
use Cake\I18n\Time;
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
            // $mail = 'francesco.actis@gmail.com';

            $resultMail = $this->mailSend($mail);
            if($resultMail['esito'])
                $results['mails']['OK'][] = $resultMail['mail'];
            else
                $results['mails']['OK'][] = $resultMail['mail'];
        }

        $results['code'] = 200;
        return $this->_response($results);
    }

    public function requestPaymentSollecito() {

        $debug = false;
        $results = [];
        $results['results'] = [];
        $results['results']['KO'] = [];
        $results['results']['KO'] = [];

            // dd($this->request->getData());
        $request_payment_id = $this->request->getData('request_payment_id');
        $mail_subject = $this->request->getData('mail_subject');
        $mail_body = $this->request->getData('mail_body');
        $user_ids = $this->request->getData('user_ids');
        if(empty($user_ids)) {
            $results['code'] = 500;
            $results['msg'] = 'Nessun gasista selezionato';
            return $this->_response($results);
        }

        $this->mailInit($mail_subject, 'request_payment_sollecito');

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];

        $requestPaymentTable = TableRegistry::get('RequestPayments');
        $summaryPaymentsTable = TableRegistry::get('SummaryPayments');

        $user_ids = explode(',', $user_ids);
        foreach($user_ids as $user_id) {
            $request_payment = $requestPaymentTable->find()
                ->contain(['SummaryPayments' => [
                    'Users',
                    'conditions' => [
                        'SummaryPayments.organization_id' => $this->_organization->id,
                        'SummaryPayments.user_id' => $user_id,
                        'SummaryPayments.stato IN' => ['SOLLECITO1', 'SOLLECITO2']]]])
                ->where(['RequestPayments.organization_id' => $this->_organization->id, 'RequestPayments.id' => $request_payment_id])
                ->first();

            if(empty($request_payment->summary_payments[0]->user->email)) {
                $results['mails']['KO'][] = $request_payment->summary_payments[0]->user->username.' non ha una mail associata';
                continue;
            }

            $user = $request_payment->summary_payments[0]->user;
            $mail = trim($user->email);
            $importo_richiesto = $request_payment->summary_payments[0]->importo_richiesto;

            $mail_body_pre = "al momento non è ancora pervenuto il tuo pagamento relativo alla richiesta n°".$request_payment->num." inviata il ".$request_payment->data_send->format('d/m/Y').", ";
            $mail_body_post = "<br />Al momento risulta da saldate ".$importo_richiesto."€.<br /><br />Per maggior dettagli Collegati al sito ".$portalgas_fe_url." e, dopo aver fatto la login, scarica il documento per effettuare il pagamento.<br /><br />Se effettui il pagamento tramite bonifico indica come causale: Richiesta num ".$request_payment->num." di Mario Rossi<br />Grazie.";

            $mail_body = $mail_body_pre . $mail_body . $mail_body_post;

            $this->mailSetViewVars(['greeting' => sprintf(Configure::read('Mail.body_header'), $user->username)]);
            $this->mailSetViewVars(['mail_body' => $mail_body]);
            // $mail = 'francesco.actis@gmail.com';

            $resultMail = $this->mailSend($mail);
            if($resultMail['esito'])
                $results['results']['OK'][] = $resultMail['mail'];
            else
                $results['results']['OK'][] = $resultMail['mail'];

            /*
             * aggiorno data invio mail
             */
            $summary_payment = $summaryPaymentsTable->get($request_payment->summary_payments[0]->id);
            $datas = [];
            $datas['data_send'] = new Time(date('Y-m-d H:i:s'));
            $summary_payment = $summaryPaymentsTable->patchEntity($summary_payment, $datas);
            if (!$summaryPaymentsTable->save($summary_payment)) {
                Log::error($summary_payment->getErrors());
            }
        } // end foreach($user_ids as $user_id)

        $results['code'] = 200;
        return $this->_response($results);
    }
}
