<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use ArrayObject;

class DocumentBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * se va in conflitto, ex OfferVoices $offersTable->removeBehavior('OffersToQuote');
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)  {
        
        $results = $query->all();
        foreach ($results as $key => $result) {
            
            // debug($result);

            /*
             * a chi e' legato il documento
             */ 
            if($result->has('document_reference_model') && !empty($result->document_reference_id)) {
                $entityTable = TableRegistry::get($result->document_reference_model->code);

                $entityResults = $entityTable->find()
                                        ->where(['id' => $result->document_reference_id])
                                        ->first();

                $result->{$result->document_reference_model->code} = $entityResults;
            }
            /*
             * a chi e' il proprietario il documento
             */ 
            if($result->has('document_owner_model') && !empty($result->document_owner_id)) {
                $entityTable = TableRegistry::get($result->document_owner_model->code);

                $entityResults = $entityTable->find()
                                            ->where(['id' => $result->document_owner_id])
                                            ->first();

                $result->{$result->document_owner_model->code} = $entityResults;
            }

        } // loop foreach

        // debug($results);
    }   
}