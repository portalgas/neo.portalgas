<?php
namespace App\Controller\Admin\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ArticleTypesController extends ApiAppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void {

        parent::beforeFilter($event);
    }

    /*
     * lista di tutte tipologie degli articoli
     */
    public function gets() {

        $debug = false;

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $articlesTypesTable = TableRegistry::get('ArticlesTypes');
        $articles_types = $articlesTypesTable->jsListGets($this->_user, $this->_organization->id);
        $results['results'] = $articles_types;

        return $this->_response($results);
    }
}
