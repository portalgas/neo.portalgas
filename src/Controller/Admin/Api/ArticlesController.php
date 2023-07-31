<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleDecorator;
use App\Traits;

class ArticlesController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SuppliersOrganization');
        $this->loadComponent('Upload');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $jsonData = $this->request->input('json_decode');
        $where = [];
        /* 
        prenderei solo quelli gestiti dal referente
        $where += ['Articles.organization_id' => $this->_organization->id]; 
        c'e' gia' nella relazione ArticlesTables::OwnerSupplierOrganizations
        $where += ['OwnerSupplierOrganizations.owner_organization_id = Articles.organization_id',
                  'OwnerSupplierOrganizations.owner_supplier_organization_id = Articles.supplier_organization_id'];
        */
        if(isset($jsonData->search_flag_presente_articlesorders)) {
            $search_flag_presente_articlesorders = $jsonData->search_flag_presente_articlesorders;
            ($search_flag_presente_articlesorders) ? $search_flag_presente_articlesorders = 'Y': $search_flag_presente_articlesorders = 'N';
            $where += ['Articles.flag_presente_articlesorders' => $search_flag_presente_articlesorders];
        } 
        if(!empty($jsonData->search_name)) {
            $search_name = $jsonData->search_name;
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if(!empty($jsonData->search_codice)) {
            $search_codice = $jsonData->search_codice;
            $where += ['Articles.codice' => '%'.$search_codice.'%'];
        } 
        if(!empty($jsonData->search_categories_article_id)) {
            $search_categories_article_id = $jsonData->search_categories_article_id;
            $where += ['Articles.category_article_id' => $search_categories_article_id];
        }         
        if(!empty($jsonData->search_supplier_organization_id)) {
            $search_supplier_organization_id = $jsonData->search_supplier_organization_id;
            $where += ['OwnerSupplierOrganizations.id' => $search_supplier_organization_id];
        } 
        else {
            // non ho scelto il produttore, filtro per ACL
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
            $where += ['OwnerSupplierOrganizations.id IN ' => array_keys($suppliersOrganizations)];  
        }          
        
        $search_orders = [];
        if(!empty($jsonData->search_order)) 
            $search_orders[] = $jsonData->search_order;
        else  
            $search_orders[] = 'Articles.name';

        if(!empty($jsonData->page))
            $page = $jsonData->page;
        else 
            $page = '1';
        $limit = 10; // Configure::read('sql.limit');
        
        // dd($where);
        $articles = $this->Articles->find()
                    ->contain(['OwnerSupplierOrganizations', 'Organizations', 'CategoriesArticles'])
                    ->where($where)
                    ->limit($limit)
                    ->page($page)
                    ->order($search_orders)
                    ->all();

        $article = new ApiArticleDecorator($this->_user, $articles);
        $results['results'] = $article->results;
        
        return $this->_response($results); 
    }
    
    public function getAutocomplete() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $jsonData = $this->request->input('json_decode');
        $where = [];
        /* 
        prenderei solo quelli gestiti dal referente
        $where += ['Articles.organization_id' => $this->_organization->id]; 
        c'e' gia' nella relazione ArticlesTables::OwnerSupplierOrganizations
        $where += ['OwnerSupplierOrganizations.owner_organization_id = Articles.organization_id',
                  'OwnerSupplierOrganizations.owner_supplier_organization_id = Articles.supplier_organization_id'];
        */
        $field = $jsonData->field; // name / codice

        if($field=='name') {
            $search_name = $jsonData->search_name;
            $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
        } 
        if($field=='codice') {
            $search_codice = $jsonData->search_codice;
            $where += ['Articles.codice LIKE ' => '%'.$search_codice.'%'];
        }         
        if(!empty($jsonData->search_supplier_organization_id)) {
            $search_supplier_organization_id = $jsonData->search_supplier_organization_id;
            $where += ['OwnerSupplierOrganizations.id' => $search_supplier_organization_id];
        } 
        else {
            // non ho scelto il produttore, filtro per ACL
            $suppliersOrganizationsTable = TableRegistry::get('SuppliersOrganizations');
            $suppliersOrganizations = $suppliersOrganizationsTable->ACLgets($this->_user, $this->_organization->id, $this->_user->id);
            $suppliersOrganizations = $this->SuppliersOrganization->getListByResults($this->_user, $suppliersOrganizations);
            $where += ['OwnerSupplierOrganizations.id IN ' => array_keys($suppliersOrganizations)];  
        }                

        if($field=='name') 
            $selects = ['Articles.name'];
        else
        if($field=='codice')  
            $selects = ['Articles.codice'];

        $articles = $this->Articles->find()
                    ->select($selects)
                    ->contain(['OwnerSupplierOrganizations'])
                    ->where($where)
                    ->order(['Articles.name'])
                    ->limit(100)
                    ->all();

        $article_results = [];
        if($articles->count()>0) {
            foreach($articles as $article) {
                if($field=='name') 
                    $article_results[] = $article->name;
                else
                if($field=='codice')                 
                    $article_results[] = $article->codice;
            }
        }

        $results['results'] = $article_results;
      
        return $this->_response($results); 
    }    

    public function setValue() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';

        $jsonData = $this->request->input('json_decode');
        $id = $jsonData->id; 
        $organization_id = $jsonData->organization_id; 
        $name = $jsonData->name; 
        $value = $jsonData->value; 

        if(empty($name)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = 'Nome del campo non valorizzato!';
            return $this->_response($results);            
        }

        $where = ['id' => $id, 
                  'organization_id' => $organization_id];

        $article = $this->Articles->find()
                    ->where($where)
                    ->first();

        if(empty($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = 'Articolo non trovato!';
            return $this->_response($results); 
        }

        /*
         * trasforma 
         */
        switch(strtolower($name)) {
            case 'prezzo':
                $value = $this->convertImport($value);
            break;
        }
        $datas = [];
        $datas[$name] = $value;
        // dd($datas);
        $article = $this->Articles->patchEntity($article, $datas);
        if (!$this->Articles->save($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $errors = $article->getErrors();
            // trasformo in stringa per js
            $msg = '';
            foreach($errors as $field => $error) {
                foreach($error as $type => $err) {
                    $msg .= __($field) . ': ' . $err ."\r\n";
                }
            }
            $results['errors'] = $msg;
            return $this->_response($results); 
        }

        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
      
        return $this->_response($results); 
    }
    
    public function img1Upload($organization_id, $article_id) {
        
        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
                  
        $request = $this->request->getData();   
        if($debug) debug($request);
        if($debug) debug('organization_id passato al metodo ['.$organization_id.'] user ['.$this->_organization->id.']');

        if($organization_id!=$this->_organization->id) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "L'articolo non è gestito da te!";
            $results['results'] = [];
            return $this->_response($results);     
        }

        $config = Configure::read('Config');
        $img_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.paths'), $organization_id);
        if($debug) debug('img_path '.$img_path);

        /*
        * upload del file
        */
        $config_upload = [] ;
        $config_upload['upload_path']    = $img_path;          
        $config_upload['allowed_types']  = ['jpeg', 'jpg', 'png', 'gif'];            
        $config_upload['max_size']       = 0;   
        $config_upload['overwrite']      = true;
        $config_upload['encrypt_name']  = true;
        $config_upload['remove_spaces'] = true;         
        $this->Upload->init($config_upload);  
        $upload_results = $this->Upload->upload('img1');
        if ($upload_results===false){
            $errors = $this->Upload->errors(); 
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $errors;
            $results['results'] = [];
            if($debug) debug($errors);
            return $this->_response($results);   
        } 
        if($debug) debug($this->Upload->output());
        $upload_results = $this->Upload->output();
        $file_name = $upload_results['file_name'];
        if(!isset($upload_results['file_name']) || empty($upload_results['file_name'])) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Errore di sistema!";
            $results['results'] = [];
            return $this->_response($results);            
        }

        /*
        * ridimensiono img originale
        */
        $img_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.path.full'), $organization_id, $file_name);
        $imageOperations = [
            'thumbnail' => [
                'height' => Configure::read('App.web.img.upload.width.article'),
                'width' => Configure::read('App.web.img.upload.width.article')
            ]];
            $this->Articles->processImage(
                $img_path,
                $img_path,
            [],
            $imageOperations);

        /*
        * aggiorno db
        */            
        $where = ['organization_id' => $this->_organization->id,
                  'id' => $article_id];
        $article = $this->Articles->find()
                    ->where($where)
                    ->first();
        if(empty($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Articolo non trovato! [".json_encode($where)."]";
            $results['results'] = [];
            return $this->_response($results);            
        }        

        $datas = [];
        $datas['img1'] = $file_name;
        $article = $this->Articles->patchEntity($article, $datas);
        if (!$this->Articles->save($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $article->getErrors();
            $results['results'] = [];
            return $this->_response($results);   
        }        
        
        $results['code'] = 200;
        $results['message'] = $upload_results;
        $results['errors'] = '';
        $results['results'] = [];
        return $this->_response($results);         
    }

    public function img1Delete($organization_id, $article_id) {
        
        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];
                  
        if($debug) debug('organization_id passato al metodo ['.$organization_id.'] user ['.$this->_organization->id.']');

        if($organization_id!=$this->_organization->id) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "L'articolo non è gestito da te!";
            $results['results'] = [];
            return $this->_response($results);     
        }

        $where = ['organization_id' => $this->_organization->id,
                  'id' => $article_id];
        $article = $this->Articles->find()
                    ->where($where)
                    ->first();
        if(empty($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = "Articolo non trovato! [".json_encode($where)."]";
            $results['results'] = [];
            return $this->_response($results);            
        }        

        $config = Configure::read('Config');
        $img_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.path.full'), $organization_id, $article->img1);
        if($debug) debug('img_path '.$img_path);

        // elimino file
        unlink($img_path);

        $datas = [];
        $datas['img1'] = '';
        $article = $this->Articles->patchEntity($article, $datas);
        if (!$this->Articles->save($article)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = $article->getErrors();
            $results['results'] = [];
            return $this->_response($results);   
        }        
        
        $results['code'] = 200;
        $results['message'] = '';
        $results['errors'] = '';
        $results['results'] = [];
        return $this->_response($results); 
    }

    /*
     * dato un articolo controllo eventuali acquisti
     *  se associato non posso eliminarlo
     */    
    public function getInCarts() {

        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $jsonData = $this->request->input('json_decode');
       
        $article_organization_id = $jsonData->article_organization_id; 
        $article_id = $jsonData->article_id;

        if(empty($article_organization_id) || empty($article_id)) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = 'Parametri errati!';
            return $this->_response($results);            
        }

        $orders = ['Deliveries.data asc', 
                      'Carts.date desc'];

        $articlesTable = TableRegistry::get('Articles');
        $carts = $articlesTable->getArticleInCarts($this->_user, $this->_organization->id, $article_organization_id, $article_id, $where=[], $orders, $debug = false);
     
        /*
         * li raggruppo per consegna
         */
        $i=-1;
        $delivery_ids = [];
        $aggr_results = [];
        if(!empty($carts)) 
        foreach($carts as $cart) {
            $delivery_id = $cart['order']['delivery_id'];
            if(!in_array($delivery_id, $delivery_ids)) {
                $i++;
                $aggr_results[$i]['delivery'] = $cart['order']['delivery'];
                $aggr_results[$i]['delivery']['label'] = $this->getDeliveryLabel($cart['order']['delivery']);
                array_push($delivery_ids, $delivery_id);   
            }

            $aggr_results[$i]['delivery']['carts'][] = $cart;
        } 
        // debug(count($aggr_results));

        $results['code'] = 200;
        $results['message'] = '';
        $results['errors'] = '';
        $results['results'] = $aggr_results;
        return $this->_response($results);
      /*          
    $delivery_id_old = 0;
    foreach($carts as $cart) {

        if($delivery_id_old==0 || $delivery_id_old!=$cart['order']['delivery_id']) {
            echo '<tr>';
            echo '<td colspan="10" class="trGroup">';
            
            echo __('Delivery').' : '.$this->getDeliveryLabel($cart['order']['delivery']);

            echo $this->getOrderDateLabel($cart['order']);
            echo ' - ordine dal '.$cart['order']['data_inizio'].' al '.$cart['order']['data_fine'];
            echo '</td>';
            echo '</tr>';	                
        }

        $delivery_id_old=$cart['order']['delivery_id'];
        */
    }     
}