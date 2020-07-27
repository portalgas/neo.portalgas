<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;

class GdxpsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);

        // if(!$this->Authentication->getIdentity()->acl['isSuperReferente'] || !$this->Authentication->getIdentity()->acl['isReferentGeneric']) {
        $continua = false;

        if($this->Authentication->getIdentity()->acl['isRoot'])
            $continua = true;
        else
        if($this->Authentication->getIdentity()->acl['isSuperReferente'] && $this->Authentication->getIdentity()->organization->paramsConfig['hasArticlesGdxp']=='Y')
            $continua = true;

        if(!$continua )
         {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
          //  return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);        
    }

    /*
     * list suppliers gdxp da http://www.economiasolidale.net/api/v1/list.php
     * se no lo trovo 
     * has been blocked by CORS policy: Request header field x-csrf-token is not allowed by Access-Control-Allow-Headers in preflight response.
     *  prendo quello locale
     */
    public function index()
    {
        $gdxp_suppliers_index_url_remote = Configure::read('Gdxp.suppliers.index.url.remote');
        $gdxp_suppliers_index_url_local = Configure::read('Gdxp.suppliers.index.url.local');
        $gdxp_articles_index_url = Configure::read('Gdxp.articles.index.url');

        $this->set(compact('gdxp_suppliers_index_url_remote', 'gdxp_suppliers_index_url_local', 'gdxp_articles_index_url'));
    }

    public function export()
    {
        $debug = false;

        $articles = [];
        $supplier_organization_id = 0;
        $acl_supplier_organizations = $this->Auths->getAclSupplierOrganizationsList($this->Authentication->getIdentity());

        if ($this->request->is('post')) {

            $supplier_organization_id = $this->request->getData('supplier_organization_id');

            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $supplier_organization = $suppliersOrganizationsTable->get($this->Authentication->getIdentity(), ['SuppliersOrganizations.id' => $supplier_organization_id]);
            $this->set(compact('supplier_organization'));

            /*
             * ricerco chi gestisce il listino articoli del produttore del GAS
             */
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $ownArticles = $suppliersOrganizationsTable->getOwnArticles($this->Authentication->getIdentity(), $this->Authentication->getIdentity()->organization->id, $supplier_organization_id, $debug);

            $articlesTable = TableRegistry::get('Articles');

            $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                      'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                      'Articles.stato' => 'Y'];

            $articles = $articlesTable->gets($this->Authentication->getIdentity(), $where);
            // debug($articles);
        } // end if ($this->request->is('post')) {

        $this->set(compact('acl_supplier_organizations', 'supplier_organization_id', 'articles'));
    }
}