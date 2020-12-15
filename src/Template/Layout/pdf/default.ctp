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
		td.no-border {border-bottom: none}
		td.trGroup, th.trGroup {background-color: #E1E1E1;font-weight: bold;}
		.text-center {text-align:center}
		.text-left {text-align:left}
		.text-right {text-align:right}
		.box-referents {}
		.referent {display: inline;border-right: 1px solid #F5F5F5;margin-right: 5px;}
		.box-totali {text-align:center;background-color:#c3d2e5;padding:3px;margin:3px 0px;font-weight: bold;}
	</style>	
</head>
<body>
	<div class="hearder">

		<img src="<?php echo $img_path;?>/150h50.png" /> 

      <div class="title"><?php echo $title;?></div>
    <div>

    <div class="container clearfix">
		<?php 
		echo $this->fetch('content');
		?>
	</div>
</body>
</html>
