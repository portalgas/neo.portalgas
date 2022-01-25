<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class GdxpExportsController extends AppController
{
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Gdxp');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
        
        // $this->viewBuilder()->setClassName('Xml'); 
        $this->viewBuilder()->setClassName('Json'); 
        // $this->viewBuilder()->setOption('serialize', true);
    }

    /* 
     * esportazione in formato GDXP degli articoli di un produttore
     * https://github.com/madbob/GDXP/tree/master/v1
     * 
     * GdxpSupplierBehavior
     * GdxpArticlesBehavior 
     */
    public function articles($supplier_organization_id) {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(/* !$user->acl['isRoot'] || */ $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $results = $this->Gdxp->exportArticles($user, $organization_id, $supplier_organization_id);

        $gdxp = $results['gdxp'];
        $subject = $results['subject'];
        $blocks = $results['blocks'];
        $supplier = $results['supplier'];
        $supplier_name = $results['supplier_name'];

        $jsons = ['protocolVersion' => $gdxp['protocolVersion'], 
        'creationDate' => $gdxp['creationDate'], 
        'applicationSignature' => $gdxp['applicationSignature'], 
        'subject' => $subject, 
        'blocks' => $blocks];

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set($subject);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);

        /*
         * commentare per visualizzarlo a video
         * Set Force Download https://book.cakephp.org/3/en/views/json-and-xml-views.html#example-usage
         */
        $supplier_name = $this->setFileName($supplier_name);
        $file_name = Configure::read('Gdxp.file.prefix').$supplier_name.'-'.date('YmdHis').'.json';

        // Prior to 3.4.0
        if(!$debug) return $this->response->download($file_name);
        
        // return $this->response->withDownload($file_name);       
    } 
    
    /* 
     * esportazione in formato GDXP di un ordine
     * https://github.com/madbob/GDXP/tree/master/v1
     *
     * GdxpSupplierBehavior
     * GdxpArticleOrdersBehavior
     */
    public function order($order_type_id, $order_id, $parent_id=0) {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(/* !$user->acl['isRoot'] || */ $user->organization->paramsConfig['hasOrdersGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $results = $this->Gdxp->exportOrder($user, $organization_id, $order_type_id, $order_id, $debug);
        // debug($results); 
        $gdxp = $results['gdxp'];
        $subject = $results['subject'];
        $blocks = $results['blocks'];
        $supplier = $results['supplier'];
        $supplier_name = $results['supplier_name'];
        $delivery = $results['delivery'];

        // $this->set('_rootNode', 'gdxp'); node xml
        $this->set($gdxp);
        $this->set($subject);
        $this->set(compact('blocks', 'supplier'));
        $this->set('_serialize', ['protocolVersion', 'creationDate', 'applicationSignature', 'subject', 'blocks']);
        
        /*
        * commentare per visualizzarlo a video
        * Set Force Download https://book.cakephp.org/3/en/views/json-and-xml-views.html#example-usage
        */
        $supplier_name = $this->setFileName($supplier_name);
        $delivery_data = $this->setFileName($this->getDeliveryDate($delivery));
        $file_name = Configure::read('Gdxp.file.prefix').$supplier_name.'-'.$delivery_data.'-'.date('YmdHis').'.json';

        // Prior to 3.4.0
        if(!$debug) return $this->response->download($file_name);
        
        // return $this->response->withDownload($file_name); 
    }    
}