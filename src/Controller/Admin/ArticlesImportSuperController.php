<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Traits;

class ArticlesImportSuperController extends AppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function beforeFilter(Event $event) {
     
        parent::beforeFilter($event);
    }

    protected function _sanitizeString($value) {
        $value = trim($value);
        if(strpos(chr(151), $value)!==false)
            dd($value);
        // $value = iconv("UTF-8", "ASCII//IGNORE", $value);
        $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        $value = str_replace('*', '', $value);
        $value = str_replace('\n', ' ', $value);
        $value = str_replace('\r', ' ', $value);
        $value = trim($value);
        return $value;
    }

    /*
     * converte da 10.000,00 in 10000.00
     */   
    protected function _sanitizeImporto($value) {
        $value = trim($value);
        $value = $this->convertImport($value);
        return $value;
    }    
    protected function _getDescri($name) {

        $descri = '';
      
        if(strpos($name, '-')!==false) {
            list($name, $descri) = explode('-', $name);
        }
        if(strpos($name, ':')!==false) {
            list($name, $descri) = explode(':', $name);
        }

        return trim($descri);
    }
}