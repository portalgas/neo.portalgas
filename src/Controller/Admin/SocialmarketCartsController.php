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
         * recupero l'unico ordine che il produttore ha aperto su socialmarket
         */
        $order = $this->SocialmarketCarts->getOrder($user, Configure::read('social_market_organization_id'), $user->organization->id);
        $order_id = $order->id;

        $cartsTable = TableRegistry::get('Carts');
        $carts = $cartsTable->getByOrder($user, $organization_id, $order_id);
        if(!empty($carts)) {
            $userProfilesTable = TableRegistry::get('UserProfiles');
            $user_profiles_ids = [];
            foreach ($carts as $numResult => $cart) {

                if(!isset($user_profiles_ids[$cart->user->id])) {
                    $user_profiles = $userProfilesTable->getValuesByUserId($cart->user->id);
                    $user_profiles_ids[$cart->user->id] = $user_profiles; // per evitare di leggere i profili sul medesimo users

                    $cart->user_profiles = $user_profiles;
                }
                else
                    $cart->user_profiles = $user_profiles_ids[$cart->user->id];
            }
        }

        $this->set(compact('carts', 'order'));
    }

    public function purchaseDelivered($organization_id, $user_id, $order_id, $article_organization_id=0, $article_id=0)
    {
        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = Configure::read('social_market_organization_id');

        $results = $this->_purchase($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, 1, $debug);

        if($results===true)
            $this->Flash->success(__('save-success'), ['escape' => false]);
        else
            $this->Flash->error($results, ['escape' => false]);

        return $this->redirect(['action' => 'carts']);
    }

    public function purchaseDelete($organization_id, $user_id, $order_id, $article_organization_id=0, $article_id=0) {

        $debug = false;

        $user = $this->Authentication->getIdentity();
        $organization_id = Configure::read('social_market_organization_id');

        $results = $this->_purchase($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, 0, $debug);

        if($results===true)
            $this->Flash->success(__('delete-success'), ['escape' => false]);
        else
            $this->Flash->error($results, ['escape' => false]);

        return $this->redirect(['action' => 'carts']);
   }

    /*
     * copio da Carts a SocialMarkets
     * elimino da Carts
     *
     * $is_active = 1 inserisco / 0 eliminato
     */
    private function _purchase($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $is_active, $debug=false) {

        $cartTable = TableRegistry::get('Carts');

        $carts = [];
        if(empty($article_organization_id) || empty($article_id)) {
            // tutti gli acquisti di un utente
            $carts = $cartTable->getByOrder($user, $organization_id, $order_id, $user_id);
        }
        else {
            $cart = $cartTable->getByIds($user, $organization_id, $order_id, $user_id, $article_organization_id, $article_id, $debug);
            $carts[0] = $cart;
        }

        $userTable = TableRegistry::get('Users');

        foreach($carts as $cart) {

            $userResult = $userTable->find()
                ->select(['organization_id'])
                ->where(['id' => $cart->user_id])
                ->first();

            $datas = [];
            $datas['organization_id'] = $organization_id; // Configure::write('social_market_organization_id', 142);
            $datas['user_id'] = $cart->user_id;
            $datas['user_organization_id'] = $userResult->organization_id;
            $datas['order_id'] = $cart->order_id;
            $datas['article_name'] = $cart->articles_order->name;
            $datas['article_prezzo'] = $cart->articles_order->prezzo;
            $datas['cart_qta'] = $cart->qta;
            $datas['cart_importo_finale'] = ($cart->qta * $cart->articles_order->prezzo);
            $datas['nota'] = '';
            $datas['is_active'] = $is_active;

            $socialmarketCart = $this->SocialmarketCarts->newEntity();
            $socialmarketCart = $this->SocialmarketCarts->patchEntity($socialmarketCart, $datas);
            // dd($socialmarketCart);
            if (!$this->SocialmarketCarts->save($socialmarketCart)) {
                // debug($socialmarketCart);
                // debug($socialmarketCart->getErrors());
                return $socialmarketCart->getErrors();
            }

            /*
             * elimino da Carts
             */
            $cartTable->delete($cart);

        } // end foreach($carts as $cart)

        return true;
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
