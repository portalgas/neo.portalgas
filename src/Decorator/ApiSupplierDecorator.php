<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiSupplierDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($suppliers)
    {
    	$results = [];
	    // debug($suppliers);

	    if($suppliers instanceof \Cake\ORM\ResultSet) {
			foreach($suppliers as $numResult => $supplier) {
				$results[$numResult] = $this->_decorate($supplier);
			}
	    }
	    else 
	    if($suppliers instanceof \App\Model\Entity\Supplier) {
			$results = $this->_decorate($suppliers);  	
	    }
        else {
            foreach($suppliers as $numResult => $supplier) {
                $results[$numResult] = $this->_decorate($supplier);
            }
        }

		$this->results = $results;
    }

	private function _decorate($supplier) {

        // debug($supplier);
        
        if(isset($supplier->content)) {
        	if(isset($supplier->content->introtext)) 
	            $supplier->content->introtext = str_replace('{flike}', '', $supplier->content->introtext);
        	if(isset($supplier->content->fulltext)) 
	            $supplier->content->fulltext = str_replace('{flike}', '', $supplier->content->fulltext);
        }

        $supplier->img1 = $this->_getImg1($supplier);
        
        $html = '';
        if($supplier->voto>0) {
            
            $html .= '<ul class="ratings">';
            $html_items = str_repeat('<li class="star" aria-hidden="true"></li>', ($supplier->voto + 1));
            $html .= $html_items.'</ul>';
        }
        $supplier->voto_html = $html;

        // debug($supplier);
        return $supplier;
    }

    private function _getImg1($row) {
        
        // debug($row);
        
        $img1 = $row->img1;  

        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Supplier.img.path.full'),$img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $results = '';
        if(!empty($img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $results = $portalgas_fe_url . $img_path;
        } 
        
        return $results; 
    } 

	function name() {
		return $this->results;
	}
}