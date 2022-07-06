<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * SocialmarketCarts Controller
 *
 * @property \App\Model\Table\SocialmarketCartsTable $SocialmarketCarts
 *
 * @method \App\Model\Entity\SocialmarketCart[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SocialmarketCartsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {

        parent::beforeFilter($event);

        $debug = true;

        $user = $this->Authentication->getIdentity();
        $organization_id = $user->organization->id; // gas scelto
        $supplier_id = $user->organization->suppliers_organization->supplier_id;

        if($this->Authentication->getIdentity()==null || (!isset($this->Authentication->getIdentity()->acl) || !$this->Authentication->getIdentity()->acl['isProdGasSupplierManager'])) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }

        /*
         * ctrl se il produttore scelto ha associato l'organizzazione Socialmarket
         */
        $prodGasSuppliersTable = TableRegistry::get('ProdGasSuppliers');
        $has_socialmarket = $prodGasSuppliersTable->hasOrganizationSocialmarket($user, $organization_id);
        if(!$has_socialmarket) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function carts()
    {
        $user = $this->Authentication->getIdentity();
        $organization_id = Configure::read('social_market_organization_id');

        /*
         * recupero l'unioc ordine che il produttore ha aperto su socialmarket
         */
        $order = $this->SocialmarketCarts->getOrder($user, Configure::read('social_market_organization_id'), $user->organization->id);
        $order_id = $order->id;

        $cartsTable = TableRegistry::get('Carts');
        $carts = $cartsTable->getByOrder($user, $organization_id, $order_id);
        $this->set(compact('carts', 'order'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {

        $this->paginate = [
            'contain' => ['Organizations', 'Users', 'UserOrganizations', 'Orders'],
        ];
        $socialmarketCarts = $this->paginate($this->SocialmarketCarts);

        $this->set(compact('socialmarketCarts'));
    }

    /**
     * View method
     *
     * @param string|null $id Socialmarket Cart id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $socialmarketCart = $this->SocialmarketCarts->get($id, [
            'contain' => ['Organizations', 'Users', 'UserOrganizations', 'Orders'],
        ]);

        $this->set('socialmarketCart', $socialmarketCart);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $socialmarketCart = $this->SocialmarketCarts->newEntity();
        if ($this->request->is('post')) {
            $socialmarketCart = $this->SocialmarketCarts->patchEntity($socialmarketCart, $this->request->getData());
            if ($this->SocialmarketCarts->save($socialmarketCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'Socialmarket Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Socialmarket Cart'));
        }
        $organizations = $this->SocialmarketCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->SocialmarketCarts->Users->find('list', ['limit' => 200]);
        $userOrganizations = $this->SocialmarketCarts->UserOrganizations->find('list', ['limit' => 200]);
        $orders = $this->SocialmarketCarts->Orders->find('list', ['limit' => 200]);
        $this->set(compact('socialmarketCart', 'organizations', 'users', 'userOrganizations', 'orders'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Socialmarket Cart id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $socialmarketCart = $this->SocialmarketCarts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $socialmarketCart = $this->SocialmarketCarts->patchEntity($socialmarketCart, $this->request->getData());
            if ($this->SocialmarketCarts->save($socialmarketCart)) {
                $this->Flash->success(__('The {0} has been saved.', 'Socialmarket Cart'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Socialmarket Cart'));
        }
        $organizations = $this->SocialmarketCarts->Organizations->find('list', ['limit' => 200]);
        $users = $this->SocialmarketCarts->Users->find('list', ['limit' => 200]);
        $userOrganizations = $this->SocialmarketCarts->UserOrganizations->find('list', ['limit' => 200]);
        $orders = $this->SocialmarketCarts->Orders->find('list', ['limit' => 200]);
        $this->set(compact('socialmarketCart', 'organizations', 'users', 'userOrganizations', 'orders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Socialmarket Cart id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $socialmarketCart = $this->SocialmarketCarts->get($id);
        if ($this->SocialmarketCarts->delete($socialmarketCart)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Socialmarket Cart'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Socialmarket Cart'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
