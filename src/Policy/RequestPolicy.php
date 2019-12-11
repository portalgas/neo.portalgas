<?php
namespace App\Policy;

use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\ServerRequest;

class RequestPolicy implements RequestPolicyInterface
{
    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return bool
     */
    public function canAccess($identity, ServerRequest $request)
    {
    	// debug($identity);
        
        // Any registered user can access public functions
        if (!$request->getParam('prefix')) {
            return true;
        }

        // Admin can access every action
        if ($request->getParam('prefix') === 'api' || $request->getParam('prefix') === 'admin' || $request->getParam('prefix') === 'admin/api') {
                       
           // if(!empty($this->myUser) && isset($this->myUser['id']) && !empty($this->myUser['id']))
                $results = true;

            return $results;
        }

        // Default deny
        return false;
    }
}