<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class GasComponent extends Component {

	private $controller = '';
	private $action = '';

	public function __construct(ComponentRegistry $registry, array $config = [])
	{
        $this->_registry = $registry;
        $controller = $registry->getController();
		$this->controller = strtolower($controller->getName());
		$this->action = strtolower($controller->request->getParam('action'));
	}

	public function getBySlug($slug) {

        $organizationsTable = TableRegistry::get('Organizations');
        $organization = $organizationsTable->find()
            ->where (['j_seo' => $slug])
            ->first();

        return $organization;
	}

    /*
     * estraggo l'id content dall'img
     */
    public function getHomeByContentId($organization) {

        $img = $organization->img1;
        $id = substr($img, 0, strpos($img, '.'));
        $contentTable = TableRegistry::get('Content');
        $content = $contentTable->get($id);
        if(!empty($content)) {
            $content = $content->introtext . $content->fulltext;

            $content = str_replace('src="images', 'src="https://www.portalgas.it/images', $content);

            $content = str_replace('contattaci?contactOrganizationId', 'https://www.portalgas.it/contattaci?contactOrganizationId', $content);

            $content = str_replace('<p>{flike}</p>', '', $content);
        }
        return $content;
    }
}
