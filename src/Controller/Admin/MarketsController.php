<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * Markets Controller
 *
 * @property \App\Model\Table\MarketsTable $Markets
 *
 * @method \App\Model\Entity\Market[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MarketsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isRoot'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
            
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations'],
        ];
        $markets = $this->paginate($this->Markets);

        $this->set(compact('markets'));
    }

    /**
     * View method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $market = $this->Markets->get($id, [
            'contain' => ['Organizations', 'MarketArticles'],
        ]);

        $this->set('market', $market);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $market = $this->Markets->newEntity();
        if ($this->request->is('post')) {
            $market = $this->Markets->patchEntity($market, $this->request->getData());
            if ($this->Markets->save($market)) {
                $this->Flash->success(__('The {0} has been saved.', 'Market'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Market'));
        }
        $organizations = $this->Markets->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('market', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $market = $this->Markets->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $market = $this->Markets->patchEntity($market, $this->request->getData());
            if ($this->Markets->save($market)) {
                $this->Flash->success(__('The {0} has been saved.', 'Market'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Market'));
        }
        $organizations = $this->Markets->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('market', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $market = $this->Markets->get($id);
        if ($this->Markets->delete($market)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Market'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Market'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
