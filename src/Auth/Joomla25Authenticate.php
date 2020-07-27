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
	private $debug = false;
	private $encrypt_method = "AES-256-CBC";
	private $key = '';
	private $iv = '';

    public function __construct(IdentifierCollection $identifiers, array $config = [])
    {
        parent::__construct($identifiers, $config);

		$config = Configure::read('Config');
		$salt = $config['SaltPortalgas'];
		
		$secret_key = $salt.date('Ymd');
		$secret_iv = $salt;
		
		// hash
		$this->key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$this->iv = substr(hash('sha256', $secret_iv), 0, 16);	        
    }

    public function authenticate(ServerRequestInterface $request, ResponseInterface $response)
    {
        $user_salt = $request->getQuery('u');
		if(empty($user_salt)) {
			return new Result(null, Result::FAILURE_CREDENTIALS_INVALID);
		}

		$user = $this->_getUser($request);
        if($this->debug) debug($user);
        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }
		   
        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identifier->getErrors());
        }

        return new Result($user, Result::SUCCESS);
    }

    private function _getUser(ServerRequestInterface $request)
    {    
        $user_salt = $request->getQuery('u');
        if($this->debug) debug($user_salt);

		$user = $this->_decrypt($user_salt);
		$user = unserialize($user);
		if($this->debug) debug($user);

		$usersTable = TableRegistry::get('Users');
		$user = $usersTable->findLogin($user['user_organization_id'], $user['user_id'], $user['organization_id'], $this->debug); 

		return $user;
    }

	private function _encrypt($string) {
		$results = openssl_encrypt($string, $this->encrypt_method, $this->key, 0, $this->iv);
		$results = base64_encode($results);
		return $results;
	}
	
	private function _decrypt($string) {
		$results = openssl_decrypt(base64_decode($string), $this->encrypt_method, $this->key, 0, $this->iv);
		return $results;
	}       
}