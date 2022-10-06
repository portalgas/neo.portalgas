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
		body {font-size: 12px;font-family:Helvetica, Arial, sans-serif;}
		.hearder .title {float:right}
		h1 {background-color:#c3d2e5;padding:2px;}
		h3 {background: none repeat scroll 0 0 #1e83c2; color: #fff; font-size: 1.5em;padding: 5px;}
		h2 {color:#484848;font-size: 1.4em;}
		h2 small {margin-left: 10px;font-size: 0.8em;}
		th {border-bottom:1px solid #555;background-color: #F5F5F5;}
		td {border-bottom: 1px solid #ddd}
		td.no-border {border-bottom: none}
		td.trGroup, th.trGroup {background-color: #E1E1E1;font-weight: bold;}
		.text-center {text-align:center}
		.text-left {text-align:left}
		.text-right {text-align:right}
		.list-inline {padding: 5 0 5 0;list-style: none;}
		dl, ol, ul {margin-top: 0;margin-bottom: 1rem;}
		.list-inline-item:not(:last-child) {margin-right: .5rem;}
		.list-inline-item {display: inline-block;margin-right: .5rem;}		
		.box-totali {text-align:center;background-color:#c3d2e5;padding:3px;margin:3px 0px;font-weight: bold;font-size: 14px;}
	</style>	
</head>
<body>
	<div class="hearder">

		<img src="<?php echo $img_path;?>/150h50.png" /> 

      <div class="title"><?php echo $title;?></div>
    <div>

    <div class="container clearfix"><?php echo $img_path;?>
		<?php 
		echo $this->fetch('content');
		?>
	</div>
</body>
</html>
