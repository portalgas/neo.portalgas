<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class MovementDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    private $_path = '';
    private $_url = '';

    private $_movement_type_invoice = 5;    // INVOICE Pagamento fattura a fornitore
    private $_movement_type_userimport = 8; // USERSIMPORT Saldo movimento di cassa

    public function __construct($user, $movements)
    {
        $config = Configure::read('Config');
        $portalgas_app_root = $config['Portalgas.App.root'];
        $portalgas_fe_url = $config['Portalgas.fe.url'];
        $app_doc_upload_tesoriere = $config['App.doc.upload.tesoriere'];
        $this->_path = $portalgas_app_root.$app_doc_upload_tesoriere . DS . $user->organization->id . DS;
        $this->_url = $portalgas_fe_url.$app_doc_upload_tesoriere .'/'. $user->organization->id . '/';
        
        $results = [];
        $i=0;
	    // debug($movements);

	    if($movements instanceof \Cake\ORM\ResultSet) {
			foreach($movements as $movement) {
                $results[$i] = $this->_decorate($movement);
                $i++;
			}
	    }
	    else 
	    if($movements instanceof \App\Model\Entity\Movement) {
		    $results = $this->_decorate($movements);  	
	    }

		$this->results = $results;
    }

	private function _decorate($movement) {

        $movement->doc_url = '';
        if($movement->has('order') && 
           !empty($movement->order->tesoriere_doc1) && 
           file_exists($this->_path . $movement->order->tesoriere_doc1)) {
            $movement->doc_url = $this->_url . $movement->order->tesoriere_doc1;
           }

        if($movement->has('stat_order') && 
           !empty($movement->stat_order->tesoriere_doc1) && 
           file_exists($this->_path . $movement->stat_order->tesoriere_doc1)) {
            $movement->doc_url = $this->_url . $movement->stat_order->tesoriere_doc1;
           }
           
        /*
        * se importato PAGAMENTO FATTURA non e' modificabile movement_type_id
        */        
        $movement->movement_type_edit = true;
        if($movement->movement_type_id==$this->_movement_type_invoice) // INVOICE Pagamento fattura a fornitore
          $movement->movement_type_edit = false;

        /*
        * se importato da CASSA non e' modificabile, ogni volta viene ricreato
        */         
        $movement->edit = true; 
        if($movement->movement_type_id==$this->_movement_type_userimport) // USERSIMPORT Saldo movimento di cassa 
          $movement->edit = false;

        return $movement;
    }
 
	function name() {
		return $this->results;
	}    
}