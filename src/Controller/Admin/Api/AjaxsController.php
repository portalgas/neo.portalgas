<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class AjaxsController extends ApiAppController
// class AjaxsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }

    /*
     * aggiorna i dati del campo .fieldUpdateAjax 
     *	entity
     *	field
     *	value
     *	id
     */
    public function fieldUpdate() {

        $debug = false;

        $results = [];

        $id = $this->request->getData('id');
        $entity = $this->request->getData('entity');
        $field = $this->request->getData('field');
        $value = $this->request->getData('value');
        if($debug) debug('id '.$id.' - entity '.$entity.' - field '.$field.' - value '.$value);
        $entityTable = TableRegistry::get($entity);

        $entity = $entityTable->get($id);
        if($debug) debug($entity);

        /*
         * data_range_ini / data_range_fine
         */
        if ($this->stringStartsWith($field, 'data_range')) {
            list($data_range_ini, $data_range_fine) = explode(' - ', $value);

            $data_range_ini = $this->convertDate($data_range_ini);
            $results = $this->fieldUpdateExecute($entityTable, $entity, 'data_range_ini', $data_range_ini, $debug);
     
            if($results['code']==200) {
                $data_range_fine = $this->convertDate($data_range_fine);
                $results = $this->fieldUpdateExecute($entityTable, $entity, 'data_range_fine', $data_range_fine, $debug);
            }
        }
        else {
            $results = $this->fieldUpdateExecute($entityTable, $entity, $field, $value, $debug);
        }

        if($results['code']!=200) {
            $this->_respondWithValidationErrors();
        }

        $code = $results['code'];
        $message = $results['message'];
        $errors = $results['errors'];
        // $this->set('_serialize', ['code', 'message', 'errors']);
        
        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
																				 
		
        return $this->response; 
              
    }

    private function fieldUpdateExecute($entityTable, $entity, $field, $value, $debug=false) {

        $results = [];

        $data = [];
        $data[$field] = $value;
        if($debug) debug($data);
        //debug($entity);
        $entity = $entityTable->patchEntity($entity, $data);
        if($debug) debug($entity);
        if ($entityTable->save($entity)) {
            $results['code'] = 200;
            $results['errors'] = '';
            $results['message'] = __('ajax success');
        }
        else {
            $results['code'] = 500;
            $results['errors'] = $entity->getErrors();
            $results['message'] = __('ajax error');
        }  

        return $results; 
    }
 

    /*
     * eventuali id da escludere
     */
    public function getList() {
        $results = [];
    
        $conditions = [];
        $id = $this->request->getData('id');
        $entity = $this->request->getData('entity');
        
						  
								 
												  
				  
		 
        $exclude_ids = $this->request->getData('exclude_ids');
        if(!empty($exclude_ids))
            $conditions += ['id not in' => $exclude_ids];

        $entityTable = TableRegistry::get($entity);

        $results = $entityTable->find('list', ['conditions' => $conditions, 'limit' => Configure::read('paginate.limit')]); 

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
																				 
		
        return $this->response; 
    }   
}