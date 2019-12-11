<?php
namespace App\Policy;

use App\Model\Table\UsersTable;
use Authorization\IdentityInterface;
use Authorization\Policy\Result;
use Cake\Core\Configure;

/**
 * Roles policy
 *
 * richiamato da RolesController
 * 	$this->Authorization->authorizeModel('index', 'add', 'edit');
 * dopo esegue RolePolicy se richiamato dal controller
 *
 * anche con 'requireAuthorizationCheck' => false
 */
class UsersTablePolicy
{
    public function canIndex(IdentityInterface $identity)
    {	
        $user = $identity->getOriginalData();
        if(in_array($user->role_id, Configure::read('RoleRootId')))
            return new Result(true);
        else
            return new Result(false);
    }

    public function canAdd(IdentityInterface $identity)
    {	
    	// debug($identity->getOriginalData());
    	debug('RolesTablePolicy canAdd');
        return new Result(true);
    }

    public function canEdit(IdentityInterface $identity)
    {
        return new Result(false);
    }	

    /*
     * $user = $this->request->getAttribute('identity');
     * $query = $user->applyScope('index', $this->Roles->find());
     * $this->set('roles', $this->paginate($query));
     */
    public function scopeIndex($user, $query)
    {
        return $query->where(['Roles.id' => 1]);
    } 
}
