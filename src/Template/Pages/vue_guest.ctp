<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

$this->layout = 'vue';
?>	
<div class="content-body">
	<div id="app-guest">
    	<appGuest></appGuest>
	</div>	
</div>