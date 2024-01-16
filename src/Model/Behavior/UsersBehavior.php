<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class UsersBehavior extends Behavior
{
	private $config = [];

    public function initialize(array $config)
    {
    	$this->config = $config;
    }

    /* 
     * $usersTable->addBehavior('Users');
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)  {
        
        // debug($event);exit;
        $users = $query->all();

        /*
         * rimappo array user.profiles
         * user->user_profiles['profile.address'] = value
         */
        $user_profiles = [];
        foreach($users as $numResult => $user) {
            if(!is_string($user) && $user->has('user_profiles')) {
                foreach($user->user_profiles as $numResult2 => $user_profile) {

                    $profile_key = str_replace('profile.', '', $user_profile->profile_key);
                    /*
                     * elimino primo e ultimo carattere se sono "
                     */
                    if(!empty($user_profile->profile_value) && strpos(substr($user_profile->profile_value, 0, 1), '"')!==false) {
                        // dd("..");
                        $user_profile->profile_value = substr($user_profile->profile_value, 1, strlen($user_profile->profile_value)-2);
                    }
                    $user_profiles[$profile_key] = $user_profile->profile_value;

                } // end foreach($user->user_profiles as $numResult2 => $user_profile) 

                $user->user_profiles = $user_profiles;
            }
        }
    }
}