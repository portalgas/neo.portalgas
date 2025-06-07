<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Decorator\ApiArticleDecorator;
use App\Traits;
use Cake\Log\Log;

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
        if(isset($jsonData->search_flag_presente_articlesorders)) {
            $search_flag_presente_articlesorders = $jsonData->search_flag_presente_articlesorders;
            ($search_flag_presente_articlesorders) ? $search_flag_presente_articlesorders = 'Y': $search_flag_presente_articlesorders = 'N';
            $where += ['Articles.flag_presente_articlesorders' => $search_flag_presente_articlesorders];
        }

        /*
        prenderei solo quelli gestiti dal referente
        $where += ['Articles.organization_id' => $this->_organization->id];
        c'e' gia' nella relazione ArticlesTables::OwnerSupplierOrganizations
        $where += ['OwnerSupplierOrganizations.owner_organization_id = Articles.organization_id',
                  'OwnerSupplierOrganizations.owner_supplier_organization_id = Articles.supplier_organization_id'];
        */
        if(!empty($jsonData->search_id)) {
            /*
             * arrivo da view (add) controllo se l'articolo ha varianti
             */
            $where_variants = ['Articles.id' => $jsonData->search_id, 'Articles.organization_id' => $this->_organization->id];
            $article_variant = $this->Articles->find()->where($where_variants)->first();
            if(!empty($article_variant)) {
                if($article_variant->parent_id==null) {
                    $where += ['Articles.organization_id' => $this->_organization->id,
                        'OR' => [
                            'Articles.id' => $jsonData->search_id,
                            'Articles.parent_id' => $jsonData->search_id]
                    ];
                }
                else {
                    $where += ['Articles.organization_id' => $this->_organization->id,
                        'OR' => [
                            'Articles.id' => $article_variant->parent_id,
                            'Articles.parent_id' => $article_variant->parent_id]
                    ];
                }
            }
            else
                $where += ['Articles.id' => $jsonData->search_id, 'Articles.organization_id' => $this->_organization->id];
        }
        else {
            if(!empty($jsonData->search_name)) {
                $search_name = $jsonData->search_name;
                $where += ['Articles.name LIKE ' => '%'.$search_name.'%'];
            }
            if(!empty($jsonData->search_codice)) {
                $search_codice = $jsonData->search_codice;
                $where += ['Articles.codice LIKE ' => '%'.$search_codice.'%'];
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

    /*
     * da index-quick se cambio il valore di un campo lo aggiorno
     */
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
         * se cambia lo stato a N
         * prima di aggiornarlo controllo eventuali ordini associati all'articolo
         */
        if($name=='stato' && $value=='N') {

            $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
            $order_codes = $lifeCycleOrdersTable->getStateCodeNotUpdateArticle($this->_user);
            $where = [];
            $where['Orders'] = ['Orders.state_code NOT IN' => $order_codes];
            $articlesTable = TableRegistry::get('Articles');
            $articles_orders = $articlesTable->getArticleInOrders($this->_user, $this->_organization->id, $article->organization_id, $article->id, $where);
            if($articles_orders->count()>0) {
                $results['code'] = 500;
                $results['message'] = 'KO';
                $results['errors'] = 'L\'articolo non può essere disattivato perchè è presente in ordini già effettuati!';
                return $this->_response($results);
            } // end if($articles_orders->count()>0) {
        } // if($name=='name' || $name=='prezzo')

        /*
         * trasforma
         */
        switch(strtolower($name)) {
            case 'prezzo':
            case 'qta':
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

        /*
         * aggiorno eventuali ordini associati all'articolo
         */
        // $this->Auths->isUserPermissionArticlesOrder($this->_user);
        if($name=='name' || $name=='prezzo') {

            $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
            $order_codes = $lifeCycleOrdersTable->getStateCodeNotUpdateArticle($this->_user);

            $where = [];
            $where['Orders'] = ['Orders.state_code NOT IN' => $order_codes];
            $articlesTable = TableRegistry::get('Articles');
            $articles_orders = $articlesTable->getArticleInOrders($this->_user, $this->_organization->id, $article->organization_id, $article->id, $where);
            if($articles_orders->count()>0) {
                $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
                foreach($articles_orders as $articles_order) {

                    if($articles_order->organization_id!=$articles_order->article_organization_id) {
                        // articolo non gestito dal GAS (ex produttore / des)
                        continue;
                    }

                    $articleOrder = $articlesOrdersTable->find()
                                        ->where([
                                            'organization_id' => $articles_order->organization_id,
                                            'order_id' => $articles_order->order_id,
                                            'article_organization_id' => $articles_order->article_organization_id,
                                            'article_id' => $articles_order->article_id])
                                        ->first();
                    $datas = [];
                    $datas[$name] = $value;
                    $articlesOrder = $articlesOrdersTable->patchEntity($articleOrder, $datas);
                    if (!$articlesOrdersTable->save($articlesOrder)) {
                        Log::write('error', $articlesOrder->getErrors());
                    }
                } // end foreach($articles_orders as $articles_order)
            } // end if($articles_orders->count()>0) {
        } // if($name=='name' || $name=='prezzo')
        elseif($name=='bio') {

            // cerco se in k_articles_types e' settato a BIO (article_type_id = 1)
            $articlesArticlesTypesTable = TableRegistry::get('ArticlesArticlesTypes');
            $articlesArticlesType = $articlesArticlesTypesTable->find()->where([
                'organization_id' => $organization_id,
                'article_id' => $id,
                'article_type_id' => 1])->first();
                // debug($articlesArticlesType);
            if($value=='N' && !empty($articlesArticlesType)) {
                if(!empty($articlesArticlesType))
                    $articlesArticlesTypesTable->delete($articlesArticlesType);
            }
            else
            if($value=='Y'&& empty($articlesArticlesType)) {
                $datas = [];
                $datas['organization_id'] = $organization_id;
                $datas['article_id'] = $id;
                $datas['article_type_id'] = 1;
                $articlesArticlesType = $articlesArticlesTypesTable->newEntity();
                $articlesArticlesType = $articlesArticlesTypesTable->patchEntity($articlesArticlesType, $datas);
                if (!$articlesArticlesTypesTable->save($articlesArticlesType)) {
                    Log::write('error', $articlesArticlesType->getErrors());
                }
            }
        }

        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';

        return $this->_response($results);
    }

    /*
     * $article_id=null se add
     */
    public function img1Upload($organization_id, $article_id=null) {

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
        $config_upload['allowed_types']  = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
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
        * aggiorno db se $article_id != null
         * se $article_id == null sono in add
        */
        if(!empty($article_id) && $article_id!=='null') {
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
        } // end if(!empty($article_id))

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

        if(!empty($article->img1)) {
            $config = Configure::read('Config');
            $img_path = $config['Portalgas.App.root'] . sprintf(Configure::read('Article.img.path.full'), $organization_id, $article->img1);
            if($debug) debug('img_path '.$img_path);

            // elimino file
            unlink($img_path);
        } // end if(!empty($article->img1))

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

    public function store() {
        $debug = false;

        $continua = true;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $request = $this->request->getData();

        if(empty($request['article_variants'])) {
            $results['code'] = 500;
            $results['message'] = 'KO';
            $results['errors'] = 'Parametri errati!';
            return $this->_response($results);
        }

        $articlesTable = TableRegistry::get('Articles');

        $datas = [];
        $datas['organization_id'] = $this->_organization->id;
        $datas['supplier_organization_id'] = $request['article']['supplier_organization_id'];
        $datas['category_article_id'] = $request['article']['category_article_id'];
        $datas['name'] = $request['article']['name'];
        $datas['bio'] = $request['article']['bio'];
        isset($request['article']['nota']) ? $datas['nota'] = $request['article']['nota']: $datas['nota'] = null;
        isset($request['article']['ingredienti']) ? $datas['ingredienti'] = $request['article']['ingredienti']: $datas['ingredienti'] = null;
        if(isset($request['article']['img1'])) $datas['img1'] = $request['article']['img1'];

        $parent_id = null; // per la prima variazione e' null
        foreach($request['article_variants'] as $numResult => $article_variant) {
            $datas['parent_id'] = $parent_id;
            $datas['codice'] = $article_variant['codice'];
            $datas['qta'] = $this->convertImport($article_variant['qta']);
            $datas['um'] = $article_variant['um'];
            // $datas['prezzo'] = $article_variant['prezzo'];
            // $datas['iva'] = $article_variant['iva'];
            $datas['prezzo'] = $this->convertImport($article_variant['prezzo_finale']);
            !empty($article_variant['um_riferimento']) ? $datas['um_riferimento'] = $article_variant['um_riferimento']: $datas['um_riferimento'] = $article_variant['um'];
            $datas['qta'] = $article_variant['qta'];
            !empty($article_variant['pezzi_confezione']) ? $datas['pezzi_confezione'] = $article_variant['pezzi_confezione']: $datas['pezzi_confezione'] = 1;
            !empty($article_variant['qta_minima']) ? $datas['qta_minima'] = $article_variant['qta_minima']: $datas['qta_minima'] = 1;
            !empty($article_variant['qta_massima']) ? $datas['qta_massima'] = $article_variant['qta_massima']: $datas['qta_massima'] = 0;
            !empty($article_variant['qta_multipli']) ? $datas['qta_multipli'] = $article_variant['qta_multipli']: $datas['qta_multipli'] = 1;
            !empty($article_variant['qta_minima_order']) ? $datas['qta_minima_order'] = $article_variant['qta_minima_order']: $datas['qta_minima_order'] = 0;
            !empty($article_variant['qta_massima_order']) ? $datas['qta_massima_order'] = $article_variant['qta_massima_order']: $datas['qta_massima_order'] = 0;
            $datas['stato'] = $article_variant['stato'];
            $datas['flag_presente_articlesorders'] = $article_variant['flag_presente_articlesorders'];

            $datas['alert_to_qta'] = 0;

            if(empty($datas['article']['id'])) {
                $article = $articlesTable->newEntity();
                $id = $this->getMax($articlesTable, 'id', ['organization_id' => $this->_organization->id]);
                $datas['id'] = ($id + 1);
            }

            $article = $articlesTable->patchEntity($article, $datas);
            if (!$articlesTable->save($article)) {
                Log::error($article->getErrors());
                // dd($article);

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

            if(isset($request['article']['articles_types_ids'])) {
                $articlesArticlesTypesTable = TableRegistry::get('ArticlesArticlesTypes');
                $articlesArticlesTypesTable->store($this->_user, $article->organization_id, $article->id, $request['article']['articles_types_ids']);
            }

            if($numResult==0) {
                $parent_id = $article->id;
            }
        } // end foreach($request['article_variants'] as $numResult => $article_variant)

        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results']['article_id'] = $parent_id; // gli passo l'id del padre per il redirect su index-quick con filtro
        return $this->_response($results);
    }

    /*
     * $article_id=null se add
     * $supplier_organization_id!=null se scelto un produttore
     */
    public function get($article_organization_id, $article_id=null, $supplier_organization_id=null) {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $article = [];
        $article_variants = [];

        if(empty($article_id)) {
            /*
             * add
             */
            $category_article_id = null;
            $categoriesArticlesTable = TableRegistry::get('CategoriesArticles');
            $categoriesArticles = $categoriesArticlesTable->getsList($this->_user, $this->_organization->id);
            $categoriesArticles = $categoriesArticles->toArray();
            if(count($categoriesArticles)==1)
                $category_article_id = key($categoriesArticles);

            $article['organization_id'] = $article_organization_id;
            $article['category_article_id'] = $category_article_id;
            $article['bio'] = 'N';
            $article['articles_types_ids'] = [];

            $i=0;
            $article_variants[$i]['id'] = null;
            $article_variants[$i]['parent_id'] = null;
            $article_variants[$i]['codice'] = '';
            $article_variants[$i]['qta'] = '0,00';
            $article_variants[$i]['um'] = 'PZ';
            $article_variants[$i]['prezzo'] = '0,00';
            $article_variants[$i]['iva'] = 'inclusa';
            $article_variants[$i]['prezzo_finale'] = '0,00';
            $article_variants[$i]['um_riferimento'] = 'PZ';
            $article_variants[$i]['pezzi_confezione'] = 1;
            $article_variants[$i]['qta_minima'] = 1;
            $article_variants[$i]['qta_massima'] = 0;
            $article_variants[$i]['qta_minima_order'] = 0;
            $article_variants[$i]['qta_multipli'] = 1;
            $article_variants[$i]['qta_massima_order'] = 0;
            $article_variants[$i]['stato'] = 'Y';
            $article_variants[$i]['flag_presente_articlesorders'] = 'Y';
            $article_variants[$i]['um_rif_values'] = [];
            $article_variants[$i]['errors'] = new \stdClass();
        }
        else {
            /*
             * edit
             */
            $where = ['organization_id' => $article_organization_id, 'id' => $article_id];
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

            /*
             * se parent_id e' valorizzato ho preso un figlio, recupero il padre
             */
            if(!empty($article->parent_id)) {
                $where = ['organization_id' => $article_organization_id, 'id' => $article->parent_id];
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
            }

            /*
             * creo varianti di default
             */
            $i=0;
            $article_variants[$i]['id'] = $article->id;
            $article_variants[$i]['parent_id'] = $article->parent_id;
            $article_variants[$i]['codice'] = $article->codice;
            $article_variants[$i]['um'] = $article->um;
            $article_variants[$i]['prezzo'] = $article->prezzo;
            $article_variants[$i]['iva'] = 'inclusa';
            $article_variants[$i]['prezzo_finale'] = $article->prezzo;
            $article_variants[$i]['um_riferimento'] = $article->um_riferimento;
            $article_variants[$i]['pezzi_confezione'] = $article->pezzi_confezione;
            $article_variants[$i]['qta'] = $article->qta;
            $article_variants[$i]['qta_minima'] = $article->qta_minima;
            $article_variants[$i]['qta_massima'] = $article->qta_massima;
            $article_variants[$i]['qta_minima_order'] = $article->qta_minima_order;
            $article_variants[$i]['qta_multipli'] = $article->qta_multipli;
            $article_variants[$i]['qta_massima_order'] = $article->qta_massima_order;
            $article_variants[$i]['stato'] = $article->stato;
            $article_variants[$i]['flag_presente_articlesorders'] = $article->flag_presente_articlesorders;
            $article_variants[$i]['um_rif_values'] = [];

            /*
             * cerco eventuali varianti
             */
            $where = ['organization_id' => $article_organization_id, 'parent_id' => $article->id];
            $parent_article_variants = $this->Articles->find()
                ->where($where)
                ->all();

            if($parent_article_variants->count()>0) {
                foreach($parent_article_variants as $article_variant) {
                    $i++;
                    $article_variants[$i]['id'] = $article_variant->id;
                    $article_variants[$i]['parent_id'] = $article_variant->parent_id;
                    $article_variants[$i]['codice'] = $article_variant->codice;
                    $article_variants[$i]['um'] = $article_variant->um;
                    $article_variants[$i]['prezzo'] = $article_variant->prezzo;
                    $article_variants[$i]['iva'] = 'inclusa';
                    $article_variants[$i]['prezzo_finale'] = $article_variant->prezzo;
                    $article_variants[$i]['um_riferimento'] = $article_variant->um_riferimento;
                    $article_variants[$i]['pezzi_confezione'] = $article_variant->pezzi_confezione;
                    $article_variants[$i]['qta'] = $article_variant->qta;
                    $article_variants[$i]['qta_minima'] = $article_variant->qta_minima;
                    $article_variants[$i]['qta_massima'] = $article_variant->qta_massima;
                    $article_variants[$i]['qta_minima_order'] = $article_variant->qta_minima_order;
                    $article_variants[$i]['qta_multipli'] = $article_variant->qta_multipli;
                    $article_variants[$i]['qta_massima_order'] = $article_variant->qta_massima_order;
                    $article_variants[$i]['stato'] = $article_variant->stato;
                    $article_variants[$i]['flag_presente_articlesorders'] = $article_variant->flag_presente_articlesorders;
                    $article_variants[$i]['um_rif_values'] = [];
                }
            }
        } // end if(empty($article_id))

        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results']['article'] = $article;
        $results['results']['article_variants'] = $article_variants;

        return $this->_response($results);

    }
}
