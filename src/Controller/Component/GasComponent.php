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
            ->select(['id', 'name', 'descrizione', 'indirizzo', 'localita', 'cap', 'provincia', 'www', 'www2', 'cf', 'lat', 'lng', 'img1', 'template_id', 'j_seo'])
            ->where (['j_seo' => $slug, 'type' => 'GAS', 'stato' => 'Y'])
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

            $config = Configure::read('Config');
            $portalgas_fe_url = $config['Portalgas.fe.url'];

            $content = str_replace('src="images', 'src="'.$portalgas_fe_url.'/images', $content);

            $content = str_replace('contattaci?contactOrganizationId', $portalgas_fe_url.'/contattaci?contactOrganizationId', $content);

            $content = str_replace('<p>{flike}</p>', '', $content);
        }
        return $content;
    }
}
