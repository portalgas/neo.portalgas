<?php
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title>
        <?= $this->fetch('title') ?>
    </title>
	<?php
	echo $this->Html->css('AdminLTE./bower_components/bootstrap/dist/css/bootstrap.min', ['media' => 'all', 'fullBase' => true]); 
	echo $this->Html->css('AdminLTE./bower_components/font-awesome/css/font-awesome.min', ['media' => 'all', 'fullBase' => true]); 
	echo $this->Html->css('AdminLTE./bower_components/Ionicons/css/ionicons.min', ['media' => 'all', 'fullBase' => true]); 
	echo $this->Html->css('AdminLTE.AdminLTE.min', ['fullBase' => true]); 
	echo $this->Html->css('AdminLTE.skins/skin-'. Configure::read('Theme.skin') .'.min', ['media' => 'all', 'fullBase' => true]); 
	echo $this->Html->css('style', ['media' => 'all', 'fullBase' => true]); 
	?>
	
<style>

</style>	
</head>
<body>
	<div class="container clearfix">
		<?php 
		echo $this->fetch('content');
		?>
	</div>
</body>
</html>
