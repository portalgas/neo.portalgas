<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class OrganizationsPayComponent extends Component {

	private $controller = '';
	private $action = '';

	private $portalgas_app_root ='';
	private $portalgas_fe_url ='';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));

        $config = Configure::read('Config');
        $this->portalgas_app_root = $config['Portalgas.App.root'];
        $this->portalgas_fe_url = $config['Portalgas.fe.url'];

	}

	/*
	 * get physical path /images/pays/{year} 
	 */
	public function getDocPath($user, $organizationsPay, $debug=false) {

		$results = '';
		
		$path_file = $this->portalgas_app_root.Configure::read('App.doc.upload.organizations.pays').DS.$organizationsPay->year.DS.$organizationsPay->organization_id.'.pdf'; 

		if(file_exists($path_file)) {
			$results = $path_file;
		}

		if($debug) debug('path_file '.$path_file.' - results '.$results);
		return $results;	
	}

	/*
	 * get web url  
	 */		
	public function getDocUrl($user, $organizationsPay, $debug=false) {

		$results = $this->portalgas_fe_url.Configure::read('App.web.doc.upload.organizations.pays').'/'.$organizationsPay->year.'/'.$organizationsPay->organization_id.'.pdf';
		
		return $results;
	}

	public function setMsgText($user, $organization_pay_id, $value='Y', $debug=false) {
		
		$results = [];
        $organizationsPaysTable = TableRegistry::get('OrganizationsPays');

        // gia' non associato $organizationsPaysTable->Organizations->removeBehavior('OrganizationsParams');
        $organizations_pay = $organizationsPaysTable->get($organization_pay_id, ['contain' => ['Organizations']]);
        // debug($organizations_pay);

        /* 
         * ctrl se esiste il doc da scaricare
         */ 
        $doc_path = $this->getDocPath($user, $organizations_pay, $debug);
        if($debug) debug($doc_path);
        if(empty($doc_path)) {
            $results['code'] = 200;
            $results['errors'] = '';
            $results['message'] = "Il documento PDF non esiste: il messaggio non è stato creato";
        }
        else {
            $datas = [];
            $datas['hasMsg'] = $value; 
            $datas['msgText'] = $this->_createMsgText($user, $organizations_pay, $debug);
            if($debug) debug($data);
           
            $organizationsTable = TableRegistry::get('Organizations');
            // $organizationsTable->addBehavior('OrganizationsParams');
            $organization = $organizationsTable->get($organizations_pay->organization_id);

            $organization = $organizationsTable->patchEntity($organization, $datas);
            if ($organizationsTable->save($organization)) {
                $results['code'] = 200;
                $results['errors'] = '';
                $results['message'] = $datas['msgText'];
            }
            else {
                $results['code'] = 500;
                $results['errors'] = $organization->getErrors();
                $results['message'] = __('ajax error');
            }  
        } // end if(empty($doc_path)) 

		return $results;
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

        $doc_link = $this->getDocUrl($user, $organizations_pay, $debug);

        $results = sprintf(__('msg_organization_pay_to_pay'), $doc_link, $cc, $satispay, $mail, $mail); 
        return $results;        
    }	
}