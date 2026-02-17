<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ApiSuppliersOrganizationsReferentDecorator  extends AppDecorator {
	
	public $serializableAttributes = null; // ['id', 'name'];
	public $results; 

    public function __construct($user, $referents, $order=[])
    {
    	$results = [];
	    // debug($referents);

	    if($referents instanceof \Cake\ORM\ResultSet) {
			foreach($referents as $numResult => $referent) {
				$results[$numResult] = $this->_decorate($user, $referent, $order);
			}
	    }
	    else 
	    if($referents instanceof \App\Model\Entity\SuppliersOrganizationsReferent) {
			$results = $this->_decorate($user, $referents, $order);  	
	    }
        else {
            foreach($referents as $numResult => $referent) {
                $results[$numResult] = $this->_decorate($user, $referent, $order);
            }
        }

		$this->results = $results;
    }

	private function _decorate($user, $referent, $order) {

        // debug($referent);
        
        $results = [];
        
        $results['type'] = strtolower($referent->type);
        $results['name'] = $referent->user->name;
        $results['email'] = $referent->user->email;

        $satispay = false;
        foreach ($referent->user->user_profiles as $user_profile) {

            if($user_profile->profile_key=='profile.phone' && $user_profile->profile_value!='')
                $results['phone'] = $user_profile->profile_value; 
            
            if($user_profile->profile_key=='profile.satispay' && $user_profile->profile_value=='Y') 
               $satispay = true;

            if($user_profile->profile_key=='profile.satispay_phone' && $user_profile->profile_value!='')
               $results['phone_satispay'] = $user_profile->profile_value; 
        } // end foreach ($referent->user->user_profiles as $user_profile)

        if($satispay) {
           if(!isset($results['phone_satispay']) && isset($results['phone']))
             $results['phone_satispay'] = $results['phone'];
        }
        
        // debug($results);
        return $results;
    }

	function name() {
		return $this->results;
	}
}