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
	 *
	 * $organization_id = gas scelto o gas dello user
	 */
	public function createUser($user_organization_id, $user_id, $organization_id, $debug=false) {

		$usersTable = TableRegistry::get('Users');
		$results = $usersTable->findById($user_organization_id, $user_id, $organization_id, $debug);
		// $results = $usersTable->findByUsername($organization_id, $username);
		
		// debug($results);
		if(!empty($results)) {
			/*
			 * creo array con i group_id dell'utente, per UserComponent
			 */
			$group_ids = [];
			if($results->has('user_usergroup_map')) {
				foreach($results->user_usergroup_map as $user_usergroup_map) {
					$group_ids[$user_usergroup_map->group_id] = $user_usergroup_map->user_group->title;
				}
				unset($results->user_usergroup_map);
			}
			// debug($group_ids);
			$results->group_ids = $group_ids;
			
			/*
			 * rimappo array user.profiles
			 * user->user_profiles['profile.address'] = value
			 */
			$user_profiles = [];
			if($results->has('user_profiles')) {
				foreach($results->user_profiles as $user_profile) {
					$profile_key = str_replace('profile.', '', $user_profile->profile_key);
					/*
					 * elimino primo e ultimo carattere se sono "
					 */
					if(!empty($user_profile->profile_value) && strpos(substr($user_profile->profile_value, 0, 1), '"')!==false) {
						$user_profile->profile_value = substr($user_profile->profile_value, 1, strlen($user_profile->profile_value)-2);
					}

					$user_profiles[$profile_key] = $user_profile->profile_value;
				}
				
				unset($results->user_profiles);
			}
			// debug($user_profiles);
			$results->user_profiles = $user_profiles;			
		}
		
		// debug($results);
		
		return $results;
	}

}