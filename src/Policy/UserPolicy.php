<?php
namespace App\Policy;

use App\Model\Entity\User;
use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use Cake\Core\Configure;

/**
 * roles policy
 * richiamato da RolesController
 *  $this->Authorization->authorize($role);
 *  or
 *  if($user->can('add', $role))
 *
 * anche con 'requireAuthorizationCheck' => false
 */
class UserPolicy implements BeforePolicyInterface
{
    public function before($user, $resource, $action)
    {   
        debug('UserPolicy before');
        $user = $identity->getOriginalData();
        if(in_array($user->role_id, Configure::read('RoleRootId')))
            return true;
        else
            return false;
    }

    public function canAdd(IdentityInterface $user, Role $role)
    {
        debug('UserPolicy canAdd');
        return true;
    }

    public function canEdit(IdentityInterface $user, Role $role)
    {
        debug('UserPolicy canEdit');
        return true;
    } 
}