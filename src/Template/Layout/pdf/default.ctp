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
	<style type="text/css">
		.hearder .title {float:right}
		h1 {background-color:#c3d2e5;padding:2px;}
		th {border-bottom:1px solid #555;background-color: #F5F5F5;}
		td {border-bottom: 1px solid #ddd}
		td.trGroup, th.trGroup {background-color: #E1E1E1;font-weight: bold;}
		.text-center {text-align:center}
	</style>	
</head>
<body>
	<div class="hearder">

		<img src="<?php echo Configure::read('DOMPDF_PATH_IMG');?>/150h50.png" /> 

      <div class="title">titolo titolo titolo titolo </div>
    <div>

    <div class="container clearfix">
		<?php 
		echo $this->fetch('content');
		?>
	</div>
</body>
</html>
