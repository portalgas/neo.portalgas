<?php
namespace App\Controller\Admin\Api;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use App\Traits;
use Cake\Log\Log;

class CmsPagesController extends ApiAppController
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    public function initialize()
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    public function index() {

        $results = [];
        $results['code'] = 200;
        $results['message'] = 'OK';
        $results['errors'] = '';
        $results['results'] = [];

        $cmsPagesTable = TableRegistry::get('CmsPages');
        $assets = $cmsPagesTable->find()
            ->contain(['CmsMenusPages' => ['CmsMenus']])
            ->where(['CmsPages.organization_id' => $this->_organization->id])
            ->order(['CmsPages.name' => 'asc'])
            ->all();

        if(!empty($assets))
            $assets = $assets->toArray();

        $results['results'] = $assets;
        return $this->_response($results);
    }
}
