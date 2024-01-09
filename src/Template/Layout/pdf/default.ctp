<?php
use Cake\Core\Configure;
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>	
    <title>
        <?= $this->fetch('title') ?>
    </title>
	<?= $this->Html->meta('icon') ?>
	<style type="text/css">
		.hearder-title {float:right}
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

		@page {
           /* margin: 0cm 0cm; */
		   margin-bottom: 0.2cm;
		}

		body {
			font-size: 12px;
			font-family:Helvetica, Arial, sans-serif;
			margin-top: 1.5cm;
			margin-bottom: 0cm;
		}
		header {
			position: fixed;
			top: 0cm;
			left: 0.3cm;
			right: 0.3cm;
			height: 1cm;
		}
		footer {
			position: fixed; 
			bottom: 0cm; 
			left: 0.3cm;
			right: 0.3cm;
			height: 0.5cm;
			text-align: center;
		}
</style>	
</head>
<body>
	<header>
		<img src="<?php echo $img_path;?>/150h50.png" /> 
		<?php 
		if(isset($title))
			echo '<div class="hearder-title">'.$title.'</div>';
		?> 
	</header>

	<footer>
		Stampato il <?php echo date('d/m/Y');?> alle <?php echo date('H:i');?>
		<script type="text/php">
		if(isset($pdf)) {
			$pdf->page_script('
				$x = ($pdf->get_width() - 100);
				$y = ($pdf->get_height() - 20);
				$text = "Pagina ".$PAGE_NUM." di ".$PAGE_COUNT;
				$font = $fontMetrics->get_font("helvetica", "normal");
				$size = 10;
				$color = array(0,0,0);
				$word_space = 0.0;  //  default
				$char_space = 0.0;  //  default
				$angle = 0.0;   //  default
				$pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
				// $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
			');
		}
		</script>

	</footer>

    <div class="container clearfix">	
		<?php 
		echo $this->fetch('content');
		?>
	</div>	
	
</body>
</html>