<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class OrganizationsPaysController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auths');
        $this->loadComponent('OrganizationsPay');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * setta il messaggio (Organizations.hasMsg) da far comparire al Manager / Tesoriere
     * 
     * codice dinamico come App\Controller\Admin\Api\AjaxsController::fieldUpdate 
     */
    public function setMsgText() {

        $debug = false;
        $results = [];

        $organization_pay_id = $this->request->getData('organization_pay_id');
        $value = $this->request->getData('value');

        $organizationsPaysTable = TableRegistry::get('OrganizationsPays');

        $organizationsPaysTable->Organizations->removeBehavior('OrganizationsParams');
        $organizations_pay = $organizationsPaysTable->get($organization_pay_id, ['contain' => ['Organizations']]);
        // debug($organizations_pay);

        /* 
         * ctrl se esiste il doc da scaricare
         */ 
        $doc_path = $this->OrganizationsPay->getDocPath($this->user, $organizations_pay, $debug);
        if($debug) debug($doc_path);
        if(empty($doc_path)) {
            $results['code'] = 200;
            $results['errors'] = '';
            $results['message'] = "Il documento PDF non esiste: il messaggio non è stato creato";
        }
        else {

            $data = [];
            $data['hasMsg'] = $value; 
            $data['msgText'] = $this->_createMsgText($this->user, $organizations_pay, $debug);
            if($debug) debug($data);
           
            $organizationsTable = TableRegistry::get('Organizations');
            $organization = $organizationsTable->get($organizations_pay->organization_id);

            $organization = $organizationsTable->patchEntity($organization, $data);
            // debug($value);
            
            if ($organizationsTable->save($organization)) {
                $results['code'] = 200;
                $results['errors'] = '';
                $results['message'] = $data['msgText'];
            }
            else {
                $results['code'] = 500;
                $results['errors'] = $organization->getErrors();
                $results['message'] = __('ajax error');
            }  

            if($results['code']!=200) {
                $this->_respondWithValidationErrors();
            }
        } // end if(empty($doc_path)) 

        $code = $results['code'];
        $message = $results['message'];
        $errors = $results['errors'];
        // $this->set('_serialize', ['code', 'message', 'errors']);
        
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }

    private function _createMsgText($user, $organizations_pay, $debug=false) {

        $results = '';

        switch (strtolower($organizations_pay->beneficiario_pay)) {
            case 'marco':
                $cc = 'Beneficiario '.Configure::read('OrganizationPayBeneficiarioMarcoIbanLabel').'<br />IBAN '.Configure::read('OrganizationPayBeneficiarioMarcoIban');
                $satispay = Configure::read('OrganizationPayBeneficiarioMarcoCell');
                $mail = Configure::read('OrganizationPayBeneficiarioMarcoMail');
            break;
            case 'francesco':
                $cc = 'Beneficiario '.Configure::read('OrganizationPayBeneficiarioFrancescoIbanLabel').'<br />IBAN '.Configure::read('OrganizationPayBeneficiarioFrancescoIban');
                $satispay = Configure::read('OrganizationPayBeneficiarioFrancescoCell');
                $mail = Configure::read('OrganizationPayBeneficiarioFrancescoMail');
                break;
            default:
                die("OrganizationsPays::_createMsg beneficiario_pay [".$organizations_pay->beneficiario_pay."] non gestito");
            break;
        }

        $doc_link = $this->OrganizationsPay->getDocUrl($user, $organizations_pay, $debug);

        $results = sprintf(__('msg_organization_pay_to_pay'), $doc_link, $cc, $satispay, $mail, $mail); 

        return $results;        
    }
}