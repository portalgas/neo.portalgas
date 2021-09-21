<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;


/**
 * Content Controller
 *
 * @property \App\Model\Table\ContentTable $Content
 *
 * @method \App\Model\Entity\Content[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ContentController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
        $this->loadComponent('Document'); 
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    public function index()
    {
        $this->paginate = [
            'contain' => ['Assets'],
        ];
        $content = $this->paginate($this->Content);

        $this->set(compact('content'));
    }

    /**
     * View method
     *
     * @param string|null $id J Content id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $content = $this->Content->get($id, [
            'contain' => ['Assets', 'KSuppliers'],
        ]);

        $this->set('content', $content);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $content = $this->Content->newEntity();
        if ($this->request->is('post')) {
            $content = $this->Content->patchEntity($content, $this->request->getData());
            if ($this->Content->save($content)) {
                $this->Flash->success(__('The {0} has been saved.', 'J Content'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'J Content'));
        }
        $assets = $this->Content->Assets->find('list', ['limit' => 200]);
        $this->set(compact('content', 'assets'));
    }


    /**
     * Edit method
     *
     * @param string|null $id J Content id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $content = $this->Content->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $content = $this->Content->patchEntity($content, $this->request->getData());
            if ($this->Content->save($content)) {
                $this->Flash->success(__('The {0} has been saved.', 'J Content'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'J Content'));
        }
        $assets = $this->Content->Assets->find('list', ['limit' => 200]);
        $this->set(compact('content', 'assets'));
    }


    /**
     * Delete method
     *
     * @param string|null $id J Content id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $content = $this->Content->get($id);
        if ($this->Content->delete($content)) {
            $this->Flash->success(__('The {0} has been deleted.', 'J Content'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'J Content'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
