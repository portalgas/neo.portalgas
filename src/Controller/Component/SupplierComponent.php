<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use App\Decorator\ArticleDecorator;
use Cake\Routing\Router;

class SupplierComponent extends Component {

    protected $_registry;
    private $_fullbaseUrl = null;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request

        $this->_fullbaseUrl = Router::fullbaseUrl();
    }

    public function getSlug($supplier) {
        return $this->_fullbaseUrl.'/site/produttore/'.$supplier->slug;
    }

    public function getArticles($user, $supplier_id, $options=[], $debug=false) {
 
         $debug=false;
        
        if (empty($supplier_id)) {
            return null;
        }

        $results = [];
        $article_ids = [];  /* organization_id / supplier_organization_id / owner_articles (x info) */

        $where = [];
        $where = ['Suppliers.id' => $supplier_id];
        $suppliersTable = TableRegistry::get('Suppliers');
        $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');

        $suppliersResults = $suppliersTable->find()
                            ->contain(['SuppliersOrganizations'])
                            ->where($where)
                            ->first();
        if($debug) debug($suppliersResults);

        if(empty($suppliersResults))
            return null;
        
        if($debug) debug('supplier id '.$suppliersResults->id.' name ['.$suppliersResults->name.'] owner_organization_id '.$suppliersResults->owner_organization_id);
        if($suppliersResults->owner_organization_id===0) {
            /*
             * listino articoli gestito dal GAS
             */
            if(!$suppliersResults->has('suppliers_organizations') || empty($suppliersResults->suppliers_organizations))
                return null;

           if($debug) debug('count(suppliersResults->suppliers_organizations) '.count($suppliersResults->suppliers_organizations));

            if(count($suppliersResults->suppliers_organizations)==1) {
                $suppliers_organization = $suppliersResults->suppliers_organizations[0];

                $article_ids['owner_articles'] = $suppliers_organization->owner_articles;
                $article_ids['organization_id'] = $suppliers_organization->owner_organization_id;
                $article_ids['supplier_organization_id'] = $suppliers_organization->owner_supplier_organization_id;                
            }
            else {
                /*
                 * il produttore ha + suppliers_organizations, ciclo per individuare il piu' aggiornato
                 */
                $article_ids = $this->_getSupplierOrganizationLastModifiedArticles($user, $suppliersResults->suppliers_organizations); 
            }
        }
        else {
            /*
             * il supplier e' produttore => gestisce il listino articoli
             */
            $where = [];
            $where = ['organization_id' => $suppliersResults->owner_organization_id,
                       'supplier_id' =>  $suppliersResults->id]; 
            $suppliersOrganizationsResults = $suppliersOrganizationsTable->find()
                            ->where($where)
                            ->first();

            if(!empty($suppliersOrganizationsResults)) {
                $article_ids['owner_articles'] = 'REFERENT';
                $article_ids['organization_id'] = $suppliersOrganizationsResults->organization_id;
                $article_ids['supplier_organization_id'] = $suppliersOrganizationsResults->id; 
            }
        }

        if($debug) debug('supplier id '.$suppliersResults->id.' name ['.$suppliersResults->name.'] owner_articles '.$article_ids['owner_articles'].' owner_organization_id '.$article_ids['organization_id'].' owner_supplier_organization_id '.$article_ids['supplier_organization_id']);
        
        if(!empty($article_ids)) {
            $results = $this->_getArticles($user, $article_ids['organization_id'], $article_ids['supplier_organization_id']);

            $results = new ArticleDecorator($results);
            $results = $results->results;
            if($debug) debug($results);
            // exit;
        }

        return $results;
    }

    private function _getSupplierOrganizationLastModifiedArticles($user, $suppliers_organizations, $debug=false) {

        $results = [];
        foreach($suppliers_organizations as $suppliers_organization) {

            $owner_articles = $suppliers_organization->owner_articles;
            $owner_organization_id = $suppliers_organization->owner_organization_id;
            $owner_supplier_organization_id = $suppliers_organization->owner_supplier_organization_id;

            if($debug) debug('supplier id '.$suppliersResults->id.' name ['.$suppliersResults->name.'] owner_articles '.$owner_articles.' owner_organization_id '.$owner_organization_id.' owner_supplier_organization_id '.$owner_supplier_organization_id);

            $modified = $this->_getLastModifiedArticles($owner_organization_id, $owner_supplier_organization_id);
            if($debug) debug($modified); // Cake\I18n\FrozenTime
            if(!empty($modified) && (empty($results) || $modified > $results['modified'])) {
                $results['modified'] = $modified;
                $results['owner_articles'] = $owner_articles;
                $results['organization_id'] = $owner_organization_id;
                $results['supplier_organization_id'] = $owner_supplier_organization_id;
            }

        } //

        if(!empty($results)) {
            $article_ids['owner_articles'] = $results['owner_articles'];
            $article_ids['organization_id'] = $results['organization_id'];
            $article_ids['supplier_organization_id'] = $results['supplier_organization_id'];            
        }

        if($debug) debug($results);

        return $results;
    }                

    private function _getLastModifiedArticles($organization_id, $supplier_organization_id) {

        $results = '';

        $articlesTable = TableRegistry::get('Articles');
        $order = ['Articles.modified' => 'desc'];

        $where = [
            'Articles.organization_id' => $organization_id,
            'Articles.supplier_organization_id' => $supplier_organization_id,
            'Articles.stato' => 'Y',
            'Articles.modified is not null'];

        $article = $articlesTable->find()
                                    ->select(['Articles.modified'])
                                    ->where($where)
                                    ->order($order)
                                    ->first();
        /*
        debug($where);
        debug($article);
        */

        if(!empty($article))
            $results = $article->modified;

        return $results;                       
    }

    private function _getArticles($user, $organization_id, $supplier_organization_id) {

        $results = '';

        $select = ['id', 'organization_id', 'name', 'codice', 'nota', 'ingredienti', 'pezzi_confezione', 'img1', 'bio', 'qta', 'um', 'um_riferimento', 'stato', 'qta_massima', 'qta_minima', 'qta_multipli'];
        if($user!==null) 
            array_push($select, 'prezzo'); // autenticato
        
        $articlesTable = TableRegistry::get('Articles');
        $order = ['Articles.name' => 'asc'];

        $where = [
            'Articles.organization_id' => $organization_id,
            'Articles.supplier_organization_id' => $supplier_organization_id,
            'Articles.stato' => 'Y'];

        $results = $articlesTable->find()
                                    ->select($select)
                                    ->where($where)
                                    ->order($order)
                                    ->all();

        return $results;                       
    }    
}