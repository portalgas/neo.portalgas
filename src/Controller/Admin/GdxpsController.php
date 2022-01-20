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
    }    

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);    

        /*
         * $user = $this->Authentication->getIdentity();
         * if(!isset($user->acl) || !$user->acl['isSuperReferente'] || !$user->acl['isReferentGeneric']) {
         */
    }

    /*
     * list suppliers gdxp da http://hub.gasdotto.net/api/list
     * se no lo trovo 
     * has been blocked by CORS policy: Request header field x-csrf-token is not allowed by Access-Control-Allow-Headers in preflight response.
     *  prendo quello locale
     */
    public function suppliersIndex()
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();

        if(empty($user) || empty($user->organization) || !$user->acl['isRoot'] || !$user->acl['isSuperReferente'] || $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        $gdxp_suppliers_index_url_remote = Configure::read('Gdxp.suppliers.index.url.remote');
        $gdxp_suppliers_index_url_local = Configure::read('Gdxp.suppliers.index.url.local');
        $gdxp_articles_index_url = Configure::read('Gdxp.articles.index.url');

        $this->set(compact('gdxp_suppliers_index_url_remote', 'gdxp_suppliers_index_url_local', 'gdxp_articles_index_url'));
    }

    public function articlesIndex()
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        // debug($user);

        if(!$user->acl['isRoot'] || !$user->acl['isSuperReferente'] || $user->organization->paramsConfig['hasArticlesGdxp']!='Y') {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $articles = [];
        $supplier_organization_id = 0;
        $acl_supplier_organizations = $this->Auths->getAclSupplierOrganizationsList($user);

        if ($this->request->is('post')) {

            $supplier_organization_id = $this->request->getData('supplier_organization_id');

            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $supplier_organization = $suppliersOrganizationsTable->get($user, ['SuppliersOrganizations.id' => $supplier_organization_id]);
            $this->set(compact('supplier_organization'));

            /*
             * ricerco chi gestisce il listino articoli del produttore del GAS
             */
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $ownArticles = $suppliersOrganizationsTable->getOwnArticles($user, $user->organization->id, $supplier_organization_id, $debug);

            $articlesTable = TableRegistry::get('Articles');

            $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                      'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                      'Articles.stato' => 'Y'];

            $articles = $articlesTable->gets($user, $where);
            // debug($articles);
        } // end if ($this->request->is('post')) {

        $this->set(compact('acl_supplier_organizations', 'supplier_organization_id', 'articles'));
    }

    /*
     * produttore invio listino a https://hub.economiasolidale.net/api/push/mMvjyOsT61
     */
    public function articlesSendIndex()
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
  //       debug($user);

        if(!$user->acl['isRoot'] || !$user->acl['isProdGasSupplierManager'] && (isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']!='Y')) {  
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        $supplier_organization_id = $user->organization->suppliers_organization->id;
        $supplier_organization = $user->organization->suppliers_organization;
        $this->set(compact('supplier_organization'));

        /*
         * ricerco chi gestisce il listino articoli del produttore del GAS
         */
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
        $ownArticles = $suppliersOrganizationsTable->getOwnArticles($user, $user->organization->id, $supplier_organization_id, $debug);

        $articlesTable = TableRegistry::get('Articles');

        $where = ['Articles.organization_id' => $ownArticles->owner_organization_id,
                  'Articles.supplier_organization_id' => $ownArticles->owner_supplier_organization_id,
                  'Articles.stato' => 'Y'];

        $articles = [];
        $articles = $articlesTable->gets($user, $where);
        // debug($articles);

        $this->set(compact('articles'));

        if(empty($user->organization->suppliers_organization->supplier->piva))
            $canSend = false;
        else 
            $canSend = true;
        $this->set(compact('canSend'));
    }    
}