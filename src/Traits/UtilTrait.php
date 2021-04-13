<?php
namespace App\Traits;

use Cake\Core\Configure;

trait UtilTrait
{
    private $encrypt_method = "AES-256-CBC";
    private $key = '';
    private $iv = '';

    private function _getSecretKey($salt) {
        $secret_key = $salt.date('Ymd');
        return hash('sha256', $secret_key);
    }
        
    private function _getSecretIv($salt) {
        $secret_iv = $salt;
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16); 
        return $iv;
    }
                
    public function encrypt($string) {

        $config = Configure::read('Config');
        if(isset($config['Salt']))
            $salt = $config['Salt'];
        else
            $salt = '';
        
        $key = $this->_getSecretKey($salt);
        $iv = $this->_getSecretIv($salt);

        $results = openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv);
        $results = base64_encode($results);

        return $results;
    }
    
    public function decrypt($string) {

        $config = Configure::read('Config');
        if(isset($config['Salt']))
            $salt = $config['Salt'];
        else
            $salt = '';

        $key = $this->_getSecretKey($salt);
        $iv = $this->_getSecretIv($salt);

        $results = openssl_decrypt(base64_decode($string), $this->encrypt_method, $key, 0, $iv);

        return $results;
    } 

    public function stringStartsWith($string, $search) {
        return (strncmp($string, $search, strlen($search)) == 0);
    }

    public function stringEndsWith($string, $search) {
        return (substr($string, - strlen($search)) === $search);
    }

    /*
     * data una string ripete a sn $str_to_repeat 
     */
    public function strRepeat($string, $num_repeat, $str_to_repeat) {
        $string = str_repeat($str_to_repeat, $num_repeat).$string;
        $string = substr($string, strlen($string)-$num_repeat, strlen($string));
        return $string;
    }

    /*
     * data una string elimina a sn $str_to_delete 
     */
    public function strDelete($string, $str_to_delete) {
        // debug('strDelete() string '.$string);
        $string_new = '';
        $strings = str_split($string);
        foreach ($strings as $numResult => $char) {
            // debug('strDelete() char '.$char);
            if($char!=$str_to_delete) {
                $string_new = substr($string, $numResult);
                break;
            }
        }
        // debug('strDelete() string_new '.$string_new);
        return $string_new;
    }

    public function getFileExtension($file_name) {

        if(empty($file_name))
            return '';

        if(strpos($file_name, '.')===false)
            return '';

        $ext = substr($file_name, strpos($file_name, '.')+1, strlen($file_name));

        return $ext;
    }

    public function setFlashError($errors, $debug = false) {

        if ($debug) {
            debug($errors);
        }

        $msg = "";
        if(!is_array($errors)) {
            $msg = $errors;
        }
        else {
            foreach ($errors as $field => $error) 
                foreach ($error as $value) {
                    $msg .= "Campo " . __($field) . ": " . __($value) . " \r\n";
                    // $msg .= $value."<br />";
                }

        }

        // $this->Flash->error(__('MsgDataSavedKO'));
        $this->Flash->error($msg, ['escape' => false]);
    }

    public function createObjUser($args = []) {
        
        $user = new \stdClass();
        $user->organization = new \stdClass();

        if(!empty($args))
        foreach ($args as $key => $value) {
            switch ($key) {
                case 'organization_id':
                    $user->organization->id = $value;
                break;
            }
        }

        return $user;
    }

    public function setFileName($filename) {
        
        $filename = str_replace(' ', '_', $filename);
        $filename = str_replace('_-_', '_', $filename);
        $filename = str_replace('@', '-', $filename);
        $filename = strtolower($filename);

        return $filename;
    }  

    /* 
     * se delivery.sys = Y prendo il luogo = "Da definire"
     */
    public function getDeliveryDate($delivery) {

        if($delivery->sys=='Y')
            $results = $delivery->luogo;
        else
            $results = $delivery->data->i18nFormat('Y-MM-dd');
        return $results;
    }  
}