<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;

class SocialMarketsController extends ApiAppController
{
    use Traits\UtilTrait;

    public function initialize(): void 
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void  {
     
        parent::beforeFilter($event);
    }

    /* 
     * front-end - estrae
     * i produttori legati al carrello dell'user
     */
    public function userCartGets() {

        $debug = false;

        if (!$this->Authentication->getResult()->isValid()) {
            return $this->_respondWithUnauthorized();
        }

        $results = [];
        $user = $this->Authentication->getIdentity();
        $organization_id = Configure::read('social_market_organization_id');
        $user_id = $this->Authentication->getIdentity()->id;

        /*
         * elenco produttori partendo dagli acquisti dello user
         */
        $cartsTable = TableRegistry::get('Carts');

        $where = [];
        $where['Carts'] = ['Carts.user_id' => $user->id,
                            'Carts.organization_id' => $organization_id,
                            'Carts.deleteToReferent' => 'N'];        

        $where['Orders'] = ['Orders.state_code != ' => 'CREATE-INCOMPLETE'];
        // debug($where);
        $carts = $cartsTable->find()
                                    ->contain(['Orders'  => ['fields' => ['Orders.id'], 'conditions' => $where['Orders']]])
                                    ->where($where['Carts'])
                                    ->group(['Carts.order_id'])
                                 //   ->order(['Deliveries.data' => 'desc'])
                                    ->all();
        // debug($carts);exit;
        if(!empty($carts)) {
            $supplier_organization_ids = [];
            foreach($carts as $cart) {
                $supplier_organization_ids[$cart->order->id] = $cart->order->id;
            }

            $ordersTable = TableRegistry::get('Orders');

            $where = ['Orders.organization_id' => $organization_id,
                    'Orders.isVisibleBackOffice' => 'Y'];

            $results = $ordersTable->find()
                ->contain(['OrderStateCodes', 'OrderTypes', 'Deliveries',
                    'SuppliersOrganizations' => [
                        'Suppliers',
                        /* 'SuppliersOrganizationsReferents' => ['Users' => ['UserProfiles']]*/
                    ]
                ])
                ->where($where)
                ->order(['Orders.data_inizio'])
                ->all();

            return $this->_response($results);
        }
        // dd($results);

        return $this->_response($results);
    }    
}