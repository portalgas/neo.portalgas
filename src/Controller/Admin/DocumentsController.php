<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * Documents Controller
 *
 * @property \App\Model\Table\DocumentsTable $Documents
 *
 * @method \App\Model\Entity\Document[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocumentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Document'); 
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function organizationIndex()
    {
        $this->paginate = [
            'conditions' => ['Documents.organization_id' => $this->Authentication->getIdentity()->organization->id],
            'order' => ['Documents.sort'],
            'contain' => ['DocumentReferenceModels', 'DocumentOwnerModels']
        ];
        $documents = $this->paginate($this->Documents);

        $this->set(compact('documents'));

        $document_reference_id = $this->Authentication->getIdentity()->organization->id;
        $document_reference_model_id = $this->getIdDefaultIni('DocumentReferenceModels'); // Organizations
         $document_owner_id = $this->Authentication->getIdentity()->getIdentifier();
        $document_owner_model_id = $this->getIdDefaultIni('DocumentOwnerModels'); // Users
        
        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));
    }

    /**
     * $document_reference_id='',         id al quale si riferisce il documento (offer_id)
     * $document_reference_model_id='',   model al quale si riferisce il documento (Offers)
     * $document_owner_id='',             id proprietario del documento (user_id)
     * $document_owner_model_id=''        model proprietario del documento (Users)
     */
    public function organizationAdd($document_reference_id=0, $document_reference_model_id=0, $document_owner_id=0, $document_owner_model_id=0)
    {
        $debug = false;
        $continua = true;

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        /*
         * owner
         */        
        $document_owner_model = $this->Documents->getDocumentOwnerModel($document_owner_model_id);
        if(!empty($document_owner_model))
            $document_owner_model_id = $document_owner_model->id;
        else
            $document_owner_model_id = 'Users';

        if(!empty($document_owner_id))
            $document_owner_id = $this->Authentication->getIdentity()->getIdentifier();

        $document = $this->Documents->newEntity();
        if ($this->request->is('post')) {

            $request = $this->request->getData();     
            $request = $this->_prepareRequest($request, $debug);
            
            $file = $request['file_name']; // upload del file e' in edit
            unset($request['file_name']);

            $document = $this->Documents->patchEntity($document, $request);
            if($debug) debug($document);
            if ($this->Documents->save($document)) {

                /*
                 * upload del file e' solo in edit
                 */
                $document = $this->Documents->get($document->id);
                $request['file_name'] = $file; 
                $request['path'] = sprintf(Configure::read('document.path'), $document->id);
                $document = $this->Documents->patchEntity($document, $request);
                if (!$this->Documents->save($document))  {
                    $continua = false;
                    $this->setFlashError($document->getErrors());
                }
            }
            else {
                $continua = false;
                $this->setFlashError($document->getErrors());
            }

            if($continua) {
                $this->Flash->success(__('The {0} has been saved.', __('Document')));

                $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);

                return $this->redirect($url);
            }
        } // end post

        /*
         * reference
         */
        $conditions = ['is_active' => 1];
        if(!empty($document_reference_model_id))
            $conditions += ['id' => $document_reference_model_id];
        // debug($conditions);
        // $documentReferenceModels = $this->Documents->DocumentReferenceModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        /*
         * owner
         */ 
        $conditions = ['is_active' => 1];
        if(!empty($document_owner_model_id))
            $conditions += ['id' => $document_owner_model_id];
        // debug($conditions);
        // $documentOwnerModels = $this->Documents->DocumentOwnerModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));

        $documentStates = $this->Documents->DocumentStates->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);
        $documentTypes = $this->Documents->DocumentTypes->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document', 'documentStates', 'documentTypes'));
    }

    /**
     * $document_reference_id='',         id al quale si riferisce il documento
     * $document_reference_model_id='',   model al quale si riferisce il documento
     * $document_owner_id='',             id proprietario del documento
     * $document_owner_model_id=''        model proprietario del documento
     */
    public function organizationEdit($id, $document_reference_id=0, $document_reference_model_id=0, $document_owner_id=0, $document_owner_model_id=0)
    {
        $debug = false;
        $continua = true;

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        /*
         * owner
         */        
        $document_owner_model = $this->Documents->getDocumentOwnerModel($document_owner_model_id);
        if(!empty($document_owner_model))
            $document_owner_model_id = $document_owner_model->id;
        else
            $document_owner_model_id = 0;

        $document = $this->Documents->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $request = $this->request->getData(); 
            $request = $this->_prepareRequest($request, $debug);

            $request['path'] = sprintf(Configure::read('document.path'), $id);
            $document = $this->Documents->patchEntity($document, $request);
            if($debug) debug($document);
            if ($this->Documents->save($document)) {
                $this->Flash->success(__('The {0} has been saved.', __('Document')));

                $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);
                
                if($debug) debug($url);
                
                if(!$debug) return $this->redirect($url);
            }
            else {
                $this->setFlashError($document->getErrors());
            }
        } // end post

        /*
         * reference
         */
        $conditions = ['is_active' => 1];
        if(!empty($document_reference_model_id))
            $conditions += ['id' => $document_reference_model_id];
        // debug($conditions);
        // $documentReferenceModels = $this->Documents->DocumentReferenceModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        /*
         * owner
         */ 
        $conditions = ['is_active' => 1];
        if(!empty($document_owner_model_id))
            $conditions += ['id' => $document_owner_model_id];
        // debug($conditions);
        // $documentOwnerModels = $this->Documents->DocumentOwnerModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));

        $documentStates = $this->Documents->DocumentStates->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);
        $documentTypes = $this->Documents->DocumentTypes->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document', 'documentStates', 'documentTypes'));
    }

    public function organizationDelete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $document_reference_model_id = $this->request->getData('document_reference_model_id');
        $document_reference_id = $this->request->getData('document_reference_id');

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        $document = $this->Documents->get($id);
        if ($this->Documents->delete($document)) {
            $this->Flash->success(__('The {0} has been deleted.', __('Document')));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', __('Document')));
        }

        $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);
        
        if($debug) debug($url);
        
        if(!$debug) return $this->redirect($url);
    }

    public function index()
    {
        $this->paginate = [
            'contain' => ['DocumentStates', 'DocumentTypes', 'DocumentReferenceModels', 'DocumentOwnerModels']
        ];
        $documents = $this->paginate($this->Documents);

        $this->set(compact('documents'));

        $document_reference_id = $this->Authentication->getIdentity()->organization->id;
        $document_reference_model_id = $this->getIdDefaultIni('DocumentReferenceModels'); // Organizations
         $document_owner_id = $this->Authentication->getIdentity()->getIdentifier();
        $document_owner_model_id = $this->getIdDefaultIni('DocumentOwnerModels'); // Users
        
        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));
    }

    public function view($id = null)
    {
        $document = $this->Documents->get($id, [
            'contain' => ['Organizations', 'DocumentStates', 'DocumentTypes', 'DocumentReferenceModels', 'DocumentReferences', 'DocumentOwnerModels', 'DocumentOwners'],
        ]);

        $this->set('document', $document);
    }

    /**
     * $document_reference_id='',         id al quale si riferisce il documento (offer_id)
     * $document_reference_model_id='',   model al quale si riferisce il documento (Offers)
     * $document_owner_id='',             id proprietario del documento (user_id)
     * $document_owner_model_id=''        model proprietario del documento (Users)
     */
    public function add($document_reference_id=0, $document_reference_model_id=0, $document_owner_id=0, $document_owner_model_id=0)
    {
        $debug = false;
        $continua = true;

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        /*
         * owner
         */        
        $document_owner_model = $this->Documents->getDocumentOwnerModel($document_owner_model_id);
        if(!empty($document_owner_model))
            $document_owner_model_id = $document_owner_model->id;
        else
            $document_owner_model_id = 'Users';

        if(!empty($document_owner_id))
            $document_owner_id = $this->Authentication->getIdentity()->getIdentifier();

        $document = $this->Documents->newEntity();
        if ($this->request->is('post')) {

            $request = $this->request->getData();     
            $request = $this->_prepareRequest($request, $debug);
            
            $file = $request['file_name']; // upload del file e' in edit
            unset($request['file_name']);

            $document = $this->Documents->patchEntity($document, $request);
            if($debug) debug($document);
            if ($this->Documents->save($document)) {

                /*
                 * upload del file e' solo in edit
                 */
                $document = $this->Documents->get($document->id);
                $request['file_name'] = $file; 
                $request['path'] = sprintf(Configure::read('document.path'), $document->id);
                $document = $this->Documents->patchEntity($document, $request);
                if (!$this->Documents->save($document))  {
                    $continua = false;
                    $this->setFlashError($document->getErrors());
                }
            }
            else {
                $continua = false;
                $this->setFlashError($document->getErrors());
            }

            if($continua) {
                $this->Flash->success(__('The {0} has been saved.', __('Document')));

                $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);

                return $this->redirect($url);
            }
        } // end post

        /*
         * reference
         */
        $conditions = ['is_active' => 1];
        if(!empty($document_reference_model_id))
            $conditions += ['id' => $document_reference_model_id];
        // debug($conditions);
        // $documentReferenceModels = $this->Documents->DocumentReferenceModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        /*
         * owner
         */ 
        $conditions = ['is_active' => 1];
        if(!empty($document_owner_model_id))
            $conditions += ['id' => $document_owner_model_id];
        // debug($conditions);
        // $documentOwnerModels = $this->Documents->DocumentOwnerModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));

        $documentStates = $this->Documents->DocumentStates->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);
        $documentTypes = $this->Documents->DocumentTypes->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document', 'documentStates', 'documentTypes'));
    }

    /**
     * $document_reference_id='',         id al quale si riferisce il documento
     * $document_reference_model_id='',   model al quale si riferisce il documento
     * $document_owner_id='',             id proprietario del documento
     * $document_owner_model_id=''        model proprietario del documento
     */
    public function edit($id, $document_reference_id=0, $document_reference_model_id=0, $document_owner_id=0, $document_owner_model_id=0)
    {
        $debug = false;
        $continua = true;

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        /*
         * owner
         */        
        $document_owner_model = $this->Documents->getDocumentOwnerModel($document_owner_model_id);
        if(!empty($document_owner_model))
            $document_owner_model_id = $document_owner_model->id;
        else
            $document_owner_model_id = 0;

        $document = $this->Documents->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $request = $this->request->getData();
            $request = $this->_prepareRequest($request, $debug);
            $request['path'] = sprintf(Configure::read('document.path'), $id);
            $document = $this->Documents->patchEntity($document, $request);
            // if($debug) debug($document);exit;
            if ($this->Documents->save($document)) {
                $this->Flash->success(__('The {0} has been saved.', __('Document')));

                $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);
                
                if($debug) debug($url);
                
                if(!$debug) return $this->redirect($url);
            }
            else {
                $this->setFlashError($document->getErrors());
            }
        } // end post

        /*
         * reference
         */
        $conditions = ['is_active' => 1];
        if(!empty($document_reference_model_id))
            $conditions += ['id' => $document_reference_model_id];
        // debug($conditions);
        // $documentReferenceModels = $this->Documents->DocumentReferenceModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        /*
         * owner
         */ 
        $conditions = ['is_active' => 1];
        if(!empty($document_owner_model_id))
            $conditions += ['id' => $document_owner_model_id];
        // debug($conditions);
        // $documentOwnerModels = $this->Documents->DocumentOwnerModels->find('list', ['conditions' => $conditions, 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document_reference_id', 'document_reference_model_id', 'document_owner_id', 'document_owner_model_id'));

        $documentStates = $this->Documents->DocumentStates->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);
        $documentTypes = $this->Documents->DocumentTypes->find('list', ['conditions' => ['is_active' => 1], 'order' => ['sort', 'name'], 'limit' => Configure::read('paginate.limit')]);

        $this->set(compact('document', 'documentStates', 'documentTypes'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $document_reference_model_id = $this->request->getData('document_reference_model_id');
        $document_reference_id = $this->request->getData('document_reference_id');

        /*
         * reference
         */
        $document_reference_model = $this->Documents->getDocumentReferenceModel($document_reference_model_id);
        if(!empty($document_reference_model))
            $document_reference_model_id = $document_reference_model->id;
        else
            $document_reference_model_id = 0;

        $document = $this->Documents->get($id);
        if ($this->Documents->delete($document)) {
            $this->Flash->success(__('The {0} has been deleted.', __('Document')));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', __('Document')));
        }

        $url = $this->Document->getRedirectUrl($document_reference_model, $document_reference_id);

        return $this->redirect($url);
    }

    private function _prepareRequest($request, $debug=false) {

        $request['path'] = Configure::read('document.path');

        $request['organization_id'] = $this->Authentication->getIdentity()->organization->id;

        if(empty($request['document_owner_model_id']))
            $request['document_owner_model_id'] = $this->getIdDefaultIni('DocumentOwnerModels');
        if(empty($request['document_owner_id']))
            $request['document_owner_id'] = $this->Authentication->getIdentity()->getIdentifier();
        /*
         * non uploadato file => edit
         */
        if(isset($request['file_name']) && !empty($request['file_name']['tmp_name'])) {
            $request['file_size'] = $request['file_name']['size'];
            $request['file_type'] = $request['file_name']['type'];
            $request['file_ext'] = $this->getFileExtension($request['file_name']['name']);
        }
        if(empty($request['name']))
            $request['name'] = $request['file_name']['name'];
        
        if($debug) debug($request);

        return $request;
    }    
}