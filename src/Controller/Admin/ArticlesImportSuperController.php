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

        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }        
    }

    protected function _sanitizeString($value) {
        $value = trim($value);
        if(strpos(chr(151), $value)!==false)
            dd($value);
        $value = iconv("UTF-8", "ASCII//IGNORE", $value);
        // $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
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
}