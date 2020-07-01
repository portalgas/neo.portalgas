<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class GdxpSuppliersBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * se va in conflitto, ex $entityTable->removeBehavior('GdxpSuppliersOrganizations');
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)  {
        $results = $query->all();
		
        // debug($results);exit;
        foreach ($results as $key => $result) {
		  	
			$result->taxCode = $result->cf;
			$result->vatNumber = $result->piva;
			unset($result->piva);
			unset($result->cf);

			$result->address = [
				'street' => $result->indirizzo,
				'locality' => $result->localita.' '.$result->provincia,
				'zipCode' => $result->cap,
				'country' => 'IT'
			];
			unset($result->indirizzo);
			unset($result->localita);
			unset($result->cap);
			unset($result->provincia);
			     
			// $result->note = strip_tags($result->nota);
			unset($result->nota);
			
			$result->contacts = [
				0 => ['type' => 'phoneNumber', 'value' => $result->telefono],
				1 => ['type' => 'emailAddress', 'value' => $result->mail]
			];

			unset($result->mail);
			unset($result->telefono);
			unset($result->telefono2);
			unset($result->fax);
			unset($result->www);

			unset($result->id);
			unset($result->category_supplier_id);
			unset($result->nome);
			unset($result->cognome);
			unset($result->descrizione);
			unset($result->conto);
			unset($result->lat);
			unset($result->lng);
			unset($result->j_content_id);
			unset($result->img1);
			unset($result->can_promotions);
			unset($result->delivery_type_id);
			unset($result->owner_organization_id);
			unset($result->stato);
			unset($result->created);
			unset($result->modified);
		}
    }
}