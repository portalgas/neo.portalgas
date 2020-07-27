<?php
declare(strict_types=1);

namespace App\Decorator;

use Cake\Core\Configure;

class ApiUserDecorator  extends AppDecorator {
	
	public $serializableAttributes = array('id', 'name');
	public $results; 

    public function __construct($users)
    {
    	$results = [];
	    // debug($users);

	    if($users instanceof \Cake\ORM\ResultSet) {
			foreach($users as $numResult => $user) {
				$results[$numResult] = $this->_decorate($user);
			}
	    }
	    else 
	    if($users instanceof \App\Model\Entity\User) {
			$results = $this->_decorate($users);  	
	    }

		$this->results = $results;
    }

	private function _decorate($user) {

        $results = [];
        $results['id'] = $user->id;
        $results['mail'] = $user->mail;
        $results['name'] = $user->name;
        $results['username'] = $user->username;

        $results['role_id'] = $user->role_id;
        // $results['role_name'] = $user->role->name;
                    
        return $results;
    }

	function name() {
		return $this->results;
	}
}