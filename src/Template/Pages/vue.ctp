<?php
use Cake\Core\Configure;

// debug($this->Identity->get('organization'));

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

$organization = $this->Identity->get('organization');

$this->layout = 'vue';
?>	
<div class="content-body">
	<div id="app">
    	<app></app>
	</div>	
</div>