<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;

class GdxpExportsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Gdxp');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  

    /* 
     * esportazione in formato GDXP degli articoli di un produttore
     * https://github.com/madbob/GDXP/tree/master/v1
     * 
     * GdxpSupplierBehavior
     * GdxpArticlesBehavior 
     */
    public function sendArticles() {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(!$user->acl['isRoot'] || !$user->acl['isProdGasSupplierManager'] && (isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']!='Y')) {  
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $supplier_organization_id = $user->organization->suppliers_organization->id;
        $piva = $user->organization->suppliers_organization->supplier->piva;
        $url_read = Configure::read('Gdxp.articles.index.url').'/'.$piva;

        $results = $this->Gdxp->exportArticles($user, $organization_id, $supplier_organization_id);

        $gdxp = $results['gdxp'];
        $subject = $results['subject'];
        $blocks = $results['blocks'];
        $supplier = $results['supplier'];
        $supplier_name = $results['supplier_name'];

        $jsons = json_encode([
            'protocolVersion' => $gdxp['protocolVersion'], 
            'creationDate' => $gdxp['creationDate'], 
            'applicationSignature' => $gdxp['applicationSignature'], 
            'subject' => $subject, 
            'blocks' => $blocks]);

        // debug($jsons);

        $url = Configure::read('Gdxp.articles.send.url');
        Log::info($url, ['scope' => ['monitoring']]);
        Log::info($jsons, ['scope' => ['monitoring']]);

        $results = [];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsons);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: 
        application/json'));
        $resultCurl = curl_exec($ch);
        // debug($resultCurl);
        Log::info($resultCurl, ['scope' => ['monitoring']]);
        if($resultCurl === false) {
            $results['esito'] = false;
            $results['errors'] = curl_error($ch);
        }
        else {
            $results['esito'] = true;
            $results['msg'] = 'Trasmissione avvenuta con successo: <a target="_blank" href="'.$url_read.'">'.$url_read.'</a>';
        }
        Log::info($results, ['scope' => ['monitoring']]);
        curl_close($ch);            

        return $this->_response($results); 
    }       
}