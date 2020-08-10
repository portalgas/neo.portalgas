<?php
namespace App\Auth;

use ArrayObject;
use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Identifier\IdentifierCollection;
use Authentication\Identifier\IdentifierInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Traits;

/*
 * https://book.cakephp.org/4.next/en/controllers/components/authentication.html#creating-custom-authentication-objects
 *
 * /api/token/login?u=R2hqSTA1UmxGK3RmTHFNMG5Xdi9sL0hxRTZzWXVOa0hYWForY0E3RWJmTXNubVN0RUh5S3BNN1dLa3FyeEVNMlQvTm5IcFR1cFN6SUdycDJxdDBWWGVnalcyYjJtUFZyVXhNY2l4VGlncHVLOUJ1ayt5bWhFUVJIRnJjTGw1bTdrb0Z1M0FZY25kWndyUEhOUm5MeWpnPT0=&c_to=admin/cashiers&a_to=deliveries&a_to=deliveries
 *
 * u = [
 *		'user_id' => '1',
 *		'user_organization_id' => '0',
 *		'organization_id' => '21'
 *	]
 */
class Joomla25Authenticate extends AbstractAuthenticator
{
	use Traits\UtilTrait;

	private $debug = false;


    public function __construct(IdentifierCollection $identifiers, array $config = [])
    {
        parent::__construct($identifiers, $config); 
    }

    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $user_salt = $request->getQuery('u');
		if(empty($user_salt)) {
			return new Result(null, Result::FAILURE_CREDENTIALS_INVALID);
		}

		$user = $this->_getUser($request);
        if($this->debug) debug($user);
        if (empty($user) || $user===false) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    private function _getUser(ServerRequestInterface $request)
    {    
        $user_salt = $request->getQuery('u');
        if($this->debug) debug($user_salt);
        if(empty($user_salt))
            return false;

		$user = $this->decrypt($user_salt);
		$user = unserialize($user);
		if($this->debug) debug($user);
        if(empty($user) || !isset($user['user_organization_id']) || !isset($user['user_id']) || !isset($user['organization_id']))
            return false;

		$usersTable = TableRegistry::get('Users');
		$user = $usersTable->findLogin($user['user_organization_id'], $user['user_id'], $user['organization_id'], $this->debug); 

		return $user;
    }      
}