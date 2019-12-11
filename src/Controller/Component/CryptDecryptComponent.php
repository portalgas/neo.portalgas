<?php 
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;

class CryptDecryptComponent extends Component
{	
	private $encrypt_method = "AES-256-CBC";
	private $key = '';
	private $iv = '';

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
		
		$config = Configure::read('Config');
		$salt = $config['SaltPortalgas'];
		
		$secret_key = $salt.date('Ymd');
		$secret_iv = $salt;
		
		// hash
		$this->key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$this->iv = substr(hash('sha256', $secret_iv), 0, 16);		
    }

	public function encrypt($string) {
		$results = openssl_encrypt($string, $this->encrypt_method, $this->key, 0, $this->iv);
		$results = base64_encode($results);
		return $results;
	}
	
	public function decrypt($string) {
		$results = openssl_decrypt(base64_decode($string), $this->encrypt_method, $this->key, 0, $this->iv);
		return $results;
	}
}