<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$urlBase = $portalgas_bo_url.'/administrator/index.php?option=com_cake&';

echo $this->element('menu-order', ['order' => $order]);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order'), 'subtitle' => __('home')], ['home']);

echo $this->HtmlCustomSite->boxOrder($order);
