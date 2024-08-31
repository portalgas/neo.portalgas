<?php
use Cake\Core\Configure;

$user = $this->Identity->get();
$icon = ''; // '<i class="fa fa-circle"></i> ';

$config = Configure::read('Config');

$portalgas_fe_url = $config['Portalgas.fe.url'];

if ($this->Identity->isLoggedIn()) {
?>
	<div class="user-panel">
	    <div class="pull-left image">
	        <?php 
			// echo $this->Html->image('avatar5.png', array('class' => 'img-circle', 'alt' => 'User Image')); 
			echo '<img src="'.$portalgas_fe_url.'/images/organizations/contents/'.$user->organization['img1'].'" />';
			?>
	    </div>
	    <div class="pull-left info">
	        <?php // echo '<p>'.$this->Identity->get('username').'</p>';?>
	        <i class="fa fa-circle text-success"></i> Online
	    </div>
	</div>
<?php
} 
?>