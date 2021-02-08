<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class OrderPromotionsBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /*
     * https://book.cakephp.org/4/en/orm/saving-data.html#before-marshal
     * modify request data before it is converted into entities
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        // debug('OrderPromotionsBehavior beforeMarshal');

        /*
         * valor di default
         */
        if (!isset($data['prod_gas_promotion_id']) || empty($data['prod_gas_promotion_id'])) 
            $data['prod_gas_promotion_id'] = $data['parent_id']; 

        // debug($data);
    }

    public function beforeSave(Event $event, EntityInterface $entity) {
    	debug($entity);exit;
        if($entity->is_default_ini) {
        	
        	$conditions = ['id != ' => $entity->id];
        	$where = ['is_default_end' => 0];

            $table = TableRegistry::get($entity->source());
            $table->updateAll($where, $conditions);
        }
		

            /*
             * import Produttore
             */
            $supplier_organization_id = $this->ProdGasPromotionsOrganizationsManager->importProdGasSupplier($this->user, $prod_gas_promotion_id, $debug);
            if($supplier_organization_id===false) {
                $msg_errors .= __('The prodGasPromotionsOrganizationsMangers import supplier could not be saved. Please, try again.');
                $continua=false;
            }

        return true;
    }   

    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        
                if(!$this->ProdGasPromotionsOrganizationsManager->importProdGasArticlesPromotions($this->user, $prod_gas_promotion_id, $order_id, $debug)) {
                    $continua=false;
                } 
                
                App::import('Model', 'ProdGasPromotionsOrganization');
                $ProdGasPromotionsOrganization = new ProdGasPromotionsOrganization;
                
                $options = [];
                $options['conditions'] = [
                    'ProdGasPromotionsOrganization.prod_gas_promotion_id' => $prod_gas_promotion_id,
                    'ProdGasPromotionsOrganization.organization_id' => $this->user->organization['Organization']['id'] // e' quello del gas
                                           ];
                $options['recursive'] = -1;
                // debug($options);
                
                $data = []; 
                $data = $ProdGasPromotionsOrganization->find('first', $options);
                // debug($data);
                if(empty($data)) {
                    debug($options);
                    $msg_errors .= "Error ProdGasPromotionsOrganization not found!";
                    $continua=false;                    
                }
                else {
                    $data['ProdGasPromotionsOrganization']['order_id'] = $order_id;
                    $data['ProdGasPromotionsOrganization']['state_code'] = 'OPEN';
                    $data['ProdGasPromotionsOrganization']['nota_user'] = '';
                    if(!empty($data['ProdGasPromotionsOrganization']['nota_user']))
                        $data['ProdGasPromotionsOrganization']['user_id'] = $this->user->get('id');
                    else
                        $data['ProdGasPromotionsOrganization']['user_id'] = 0;
                    $ProdGasPromotionsOrganization->create();
                    if(!$ProdGasPromotionsOrganization->save($data)) {
                        $msg_errors .= "Error ProdGasPromotionsOrganization SAVE";
                        $continua=false;
                    } 
                } // end if(empty($data))                       
    } 
}