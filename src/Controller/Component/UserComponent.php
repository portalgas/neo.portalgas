<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class UserComponent extends Component {

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	/*
	 * se autenticato creo l'oggetto user ma persistere in Session
	 */
	public function createUser($organization_id, $username, $debug=false) {

		$usersTable = TableRegistry::get('Users');
		$results = $usersTable->findByUsername($organization_id, $username);
		
		// debug($results);
		if(!empty($results)) {
			/*
			 * creo array con i group_id dell'utente, per UserComponent
			 */
			$group_ids = [];
			if($results->has('user_usergroup_map')) {
				foreach($results->user_usergroup_map as $user_usergroup_map) {
					$group_ids[$user_usergroup_map->group_id] = $user_usergroup_map->UserGroups['title'];
				}
				
				unset($results->user_usergroup_map);
			}
			$results->group_ids = $group_ids;
			
			/*
			 * rimappo array user.profiles
			 * user->user_profiles['profile.address'] = value
			 */
			$user_profiles = [];
			if($results->has('user_profiles')) {
				foreach($results->user_profiles as $user_profile) {
					$profile_key = str_replace('profile.', '', $user_profile->profile_key);
					$user_profiles[$profile_key] = $user_profile->profile_value;
				}
				
				unset($results->user_profiles);
			}
			$results->user_profiles = $user_profiles;			
		}
		
		// debug($results);
		
		return $results;
	}

}