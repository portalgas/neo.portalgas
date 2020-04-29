<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class CartsController extends ApiAppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Csrf');
        $this->loadComponent('Auth');
        $this->loadComponent('Cart');
    }

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }
  
    /* 
     * estrae solo gli users che hanno effettuato acquisti in base alla consegna
     */
    public function getUsersByDelivery() {

        $debug = false;
        $results = [];

        $delivery_id = $this->request->getData('delivery_id');
        if(!empty($delivery_id)) {

            $options = [];
            $userResults = $this->Cart->getUsersByDelivery($this->user, $delivery_id, $options, $debug);

            if(!empty($userResults)) {
                /*
                 * il recordset e' object(App\Model\Entity\Cart) 
                 *    'user' => object(App\Model\Entity\User) => js user.user.name!!
                 */
                foreach($userResults as $numResult => $userResult) {
                    $results[$numResult] = $userResult->user;
                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    } 


    /* 
     * estrae solo gli users + cassa che hanno effettuato acquisti in base alla consegna
     */
    public function getUsersCashByDelivery() {

        $debug = false;
        $results = [];

        $delivery_id = $this->request->getData('delivery_id');
        
        if(!empty($delivery_id)) {

            $cashesTable = TableRegistry::get('Cashes');
            
            $options = [];
            $userResults = $this->Cart->getUsersByDelivery($this->user, $delivery_id, $options, $debug);

            if(!empty($userResults)) {
                /*
                 * il recordset e' object(App\Model\Entity\Cart) 
                 *    'user' => object(App\Model\Entity\User) => js user.user.name!!
                 */
                foreach($userResults as $numResult => $userResult) {
                    $results[$numResult] = $userResult->user;

                    /*
                     * associo la cassa
                     */
                    $cashResults = $cashesTable->getByUser($this->user, $userResult->user->organization->id, $userResult->user->id, $options, $debug);                    
                    $results[$numResult]['cash'] = $cashResults;
                }
            } // if(!empty($userResults))

        } // end if(!empty($delivery_id))

        $results = json_encode($results);
        $this->response->type('json');
        $this->response->body($results);
        // da utilizzare $this->$response->getStringBody(); // getJson()/getXml()
        
        return $this->response; 
    }     
}