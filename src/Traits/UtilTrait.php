<?php
namespace App\Traits;

use Cake\Core\Configure;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;

trait UtilTrait
{
    private $encrypt_method = "AES-256-CBC";
    private $key = '';
    private $iv = '';

    private function _getSecretKey($salt, $date='') {
       
        if(empty($date))
           $date = date('Ymd');

        $secret_key = $salt.$date;
        return hash('sha256', $secret_key);
    }
        
    private function _getSecretIv($salt) {
        $secret_iv = $salt;
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16); 
        return $iv;
    }
                
    public function encrypt($string, $date='') {

        if(empty($date))
           $date = date('Ymd');

        $config = Configure::read('Config');
        if(isset($config['Salt']))
            $salt = $config['Salt'];
        else
            $salt = '';
        
        $key = $this->_getSecretKey($salt, $date);
        $iv = $this->_getSecretIv($salt);

        $results = openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv);
        $results = base64_encode($results);

        return $results;
    }
    
    public function decrypt($string, $date='') {

        if(empty($date))
           $date = date('Ymd');

        $config = Configure::read('Config');
        if(isset($config['Salt']))
            $salt = $config['Salt'];
        else
            $salt = '';

        $key = $this->_getSecretKey($salt, $date);
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

        if(strrpos($file_name, '.')===false)
            return '';

        $ext = substr($file_name, strrpos($file_name, '.')+1, strlen($file_name));

        return $ext;
    }

    public function formatSizeUnits($bytes)
    {
        $bytes = trim($bytes);
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
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
        $user->organization->id = 0;
        $user->organization->paramsConfig['hasTrasport'] = 'N';
        $user->organization->paramsConfig['hasCostMore'] = 'N';
        $user->organization->paramsConfig['hasCostLess'] = 'N';

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

        $results = '';

        if(empty($delivery))
            return $results;

        if($delivery->sys=='Y')
            $results = $delivery->luogo;
        else
            $results = $delivery->data->i18nFormat('Y-MM-dd');
        
        return $results;
    } 

    // data Cake\I18n\FrozenDate
    public function getDeliveryLabel($delivery) {
        if($delivery->sys=='Y')
            $results = 'Da definire';
        else 
            $results = $delivery->luogo.' - '.$delivery->data->i18nFormat('eeee d MMMM');

        return $results;
    }

    // data Cake\I18n\FrozenDate
    public function getDeliveryDateLabel($delivery) {
                
        if($delivery->sys=='Y')
            $results = '';
        else {
            $now = new Date();
            $interval = $now->diff($delivery->data);
            $interval_gg = $interval->format('%R%a');

            if($interval_gg<0)
                $results = '<span class="label label-danger">Chiusa</span>';
            else 
            if($interval_gg>0)
                $results = '<span class="label label-success">Aperta (mancano ancora '.(int)$interval_gg.' gg alla consegna)</span>';
            else 
                $results = '<span class="label label-warning">Aperta (scade oggi)</span>';
        }

        return $results;
    }
     
    // data Cake\I18n\FrozenDate
    public function getOrderDateLabel($order) {
        
        $results = '';

        $now = new Date();
        $interval = $now->diff($order->data_fine);
        $interval_gg = $interval->format('%R%a');

        switch($order->state_code) {
            case 'CREATE-INCOMPLETE':
                $results .= '';
            break;
            case 'OPEN-NEXT':
                $results .= '<span class="label label-primary">Aprira&grave; ' . $order->data_inizio->i18nFormat('eeee d MMMM') . '</span>';
            break;
            case 'PRODGASPROMOTION-GAS-OPEN':
            case 'PRODGASPROMOTION-GAS-USERS-OPEN':
            case 'OPEN':
            case 'RI-OPEN-VALIDATE':
                if ($interval_gg <= (int)Configure::read('GGOrderCloseNext')) {
                    $results .= '<span class="label label-warning">Si sta chiudendo! ';
                    if ($interval_gg == 0)
                        $results .= 'oggi';
                    else
                        $results .= 'Tra&nbsp;' . (int)$interval_gg . '&nbsp;gg';
                    $results .= '</span>';
                } else
                    $results .= '<span class="label label-success">Aperto</span>';
            break;
            // case ProdGasPromotion
            case 'PRODGASPROMOTION-GAS-TRASMISSION-TO-GAS': 
            case 'PRODGASPROMOTION-GAS-WORKING': 
            case 'PRODGASPROMOTION-GAS-USERS-WORKING':
                if ($interval_gg < 0) 
                    $results .= '<span class="label label-primary">Aprira&grave; ' . $this->time->i18nFormat($results['data_inizio'], "%A %e %B") . '</span>';
                else
                if ($interval_gg >= Configure::read('GGOrderCloseNext')) {
                    $results .= '<span class="label label-warning">Si sta chiudendo! ';
                    if ($interval_gg == 0)
                        $results .= 'oggi';
                    else
                        $results .= 'Tra&nbsp;' . (int)$interval_gg . '&nbsp;gg';
                    $results .= '</span>';
                } else
                    $results .= '<span class="label label-success">Aperto</span>';				
            break;
            default:
                $results .= '<span class="label label-danger">Chiuso</span>';
            break;
        }
 
        return $results;
    }

    /*
     * calcola i giorni di differenza tra la data passata e oggi
     */
    public function getDiffDateLabel($date) {

        $results = '';

        $now = FrozenTime::parse('now');
        $interval = $now->diff($date);
        $gg = $interval->format('%a');
        $delta = $interval->format('%R'); 
        if($delta==='-') {
          if($gg==0) {
            $results = 'Oggi';
          }
          else {
            $diff_label = 'Passati '.$gg;
            ($gg==1) ? $results .= ' giorno': $results .= ' giorni';  
          }
        }
        else {
          $gg++;
          $results = 'Mancano '.$gg;
          ($gg==1) ? $results .= ' giorno': $results .= ' giorni';                  
        }

        return $results;
    }

    public function getListYears()
    {
        $year = date('Y');
        
        $results = [];
        $results[$year ] = $year;
        for($i=20; $i>=0; $i--) {
            
            $year = ($year-1);
            $results[$year ] = $year;
        }

        return $results;
    }   

    /*
     * se non c'e' accoda http://
     */
    public function decorateWWW($value)
    {
        if(empty($value))
            return $value;

        if(!$this->stringStartsWith($value, 'http://') && !$this->stringStartsWith($value, 'https://'))
            $value = 'http://'.$value;
     
        return $value;
    }

    /*  
     * trasformo in array
        'data_fine' => object(Cake\I18n\FrozenDate) {
        'time' => '2023-01-16 00:00:00.000000+00:00',
        'timezone' => 'UTC',
        'fixedNowTime' => false
    */    
    public static function dateFrozenToArray($value)
    {
        if(!is_array($value) && get_class($value)=='Cake\\I18n\\FrozenDate') {
            $new_value = [];
            $new_value['year'] = $value->format('Y');
            $new_value['month'] = $value->format('m');
            $new_value['day'] = $value->format('d');
            $value = $new_value;
        }

        return $value;
    } 
    
    /*
     * App\Model\Entity\{class}
     * prefix_post = 's'  l'entity e' singolare
     */
    public function getClass($entity, $prefix_post='') {

        $results = '';
        // debug($entity);

        if(is_object($entity)) {
            $classname = get_class($entity); 
            // debug($classname);    
            if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
                $results = $matches[1].$prefix_post;
            }
        }
        return $results;
    }  
    
    public function drawjLink($controller, $action, $qs=[]) {
        $config = Configure::read('Config');
        $portalgas_bo_url = $config['Portalgas.bo.url'];

        $results = $portalgas_bo_url.'/administrator/index.php?option=com_cake&controller='.ucfirst($controller).'&action='.$action;
        if(!empty($qs))
        foreach($qs as $key => $value)
            $results .= '&'.$key.'='.$value;

        return $results;
    }
}