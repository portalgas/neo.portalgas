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
use Cake\Log\Log;

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

    private $_debug = false;
    private $_log = false;

    public function __construct(IdentifierCollection $identifiers, array $config = [])
    {
        parent::__construct($identifiers, $config); 

        $config = Configure::read('Config');
        $this->_log = $config['Joomla25Salts.log'];
    }

    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $user_salt = $request->getQuery('u');
        if($this->_log) Log::debug('user_salt '.$user_salt);

 		if(empty($user_salt)) {
			return new Result(null, Result::FAILURE_CREDENTIALS_INVALID);
		}

		$user = $this->_getUser($request);
        if($this->_debug) debug($user);
        if (empty($user) || $user===false) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    private function _getUser(ServerRequestInterface $request)
    {    
        $user_salt = $request->getQuery('u');
        if(empty($user_salt))
            return false;

        $date = date('Ymd');
		$user = $this->decrypt($user_salt, $date);
        
        /*
         * workaround se il sal viene creato a cavallo tra i 2 gg
         */
        if(empty($user) || $user===false) {

            $date = date('Ymd',strtotime("+1 days"));
            if($this->_log) Log::debug('+1 days '. $date);
            $user = $this->decrypt($user_salt, $date); 
        }
                
        if(empty($user) || $user===false) {

            $date = date('Ymd',strtotime("-1 days"));
            if($this->_log) Log::debug('-1 days '. $date);
            $user = $this->decrypt($user_salt, $date); 
        }

        if($this->_log) Log::debug($user);
		$user = unserialize($user);
        if($this->_log) Log::debug($user);
		if($this->_debug) debug($user);
        if(empty($user) || !isset($user['user_organization_id']) || !isset($user['user_id']) || !isset($user['organization_id']))
            return false;

		$usersTable = TableRegistry::get('Users');
		$user = $usersTable->findLogin($user['user_organization_id'], $user['user_id'], $user['organization_id'], $this->_debug); 
        if($this->_log) Log::debug($user);

		return $user;
    }      
}