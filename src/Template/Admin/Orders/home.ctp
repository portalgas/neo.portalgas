<?php
use Cake\Core\Configure;

$urlBase = Configure::read('App.server').'/administrator/index.php?option=com_cake&';

echo $this->element('menu-order', ['order' => $order]);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order'), 'subtitle' => __('home')], ['home', 'list'], $order);

echo $this->HtmlCustomSite->boxOrder($order);

echo '<section class="content-header">
        <ul class="helpOnline">';
        
if(!empty($raggruppamentoOrderActions))  {
 
	/*
	 * gestione con il raggruppamento
	 */
	foreach($raggruppamentoOrderActions as $raggruppamentoOrderAction) {
 
		if($raggruppamentoOrderAction['tot_figli']>1) {
			echo "\r\n";
			echo '<li id="'.$raggruppamentoOrderAction['controller'].'">';
			echo '<a title="'.__($raggruppamentoOrderAction['label']).'">';
			echo '<img alt="'.__($raggruppamentoOrderAction['label']).'" src="'.Configure::read('App.img.cake').'/help-online/'.$raggruppamentoOrderAction['img'].'" />';
			echo '</a>';
			
			echo '<ul class="helpOnline" id="'.$raggruppamentoOrderAction['controller'].'Content" style="display:none;">';
		}
 
		foreach($orderActions as $orderAction) {

			if($orderAction->controller==$raggruppamentoOrderAction['controller']) {
				echo "\r\n";
				echo '<li>';
				if(!empty($orderAction->neo_url))
					echo '<a title="'.__($orderAction->label).'" href="'.$orderAction->neo_url.'">';
				else
					echo '<a title="'.__($orderAction->label).'" href="'.$urlBase.$orderAction->url.'">';

				echo '<img alt="'.__($orderAction->label).'" src="'.Configure::read('App.img.cake').'/help-online/'.$orderAction->img.'" />';
				echo '</a>';
				echo '</li>';
			}
		}
		
		if($raggruppamentoOrderAction['tot_figli']>1) {
			echo '</li>';
			echo '</ul>';
		}	
		
	}	
}
else {
	/*
	 * gestione SENZA il raggruppamento
	*/
	foreach($orderActions as $orderAction) {	
		echo "\r\n";
		echo '<li>';
		if(!empty($orderAction->neo_url))
			echo '<a title="'.__($orderAction->label).'" href="'.$orderAction->neo_url.'">';
		else
			echo '<a title="'.__($orderAction->label).'" href="'.$urlBase.$orderAction->url.'">';
		echo '<img alt="'.__($orderAction->label).'" src="'.Configure::read('App.img.cake').'/help-online/'.$orderAction->img.'" />';
		echo '</a>';
		echo '</li>';
	}	
}
echo '</ul>';

echo '</div>';
echo '</div>';

$js = "$(function () { ";
foreach($raggruppamentoOrderActions as $raggruppamentoOrderAction) {

	$js .= "var ".$raggruppamentoOrderAction['controller']." = false;
	  $('#".$raggruppamentoOrderAction['controller']." > a').click(function() {
		allLiSiblings = $(this).parent().siblings();
		if(".$raggruppamentoOrderAction['controller'].") {
			$(this).css('opacity','1');
			$(allLiSiblings).css('display','block');
			$('#".$raggruppamentoOrderAction['controller']."Content').fadeOut();
			".$raggruppamentoOrderAction['controller']." = false;
		}
		else {
			$(this).css('opacity','0.3');
			$(allLiSiblings).css('display','none');
			$('#".$raggruppamentoOrderAction['controller']."Content').fadeIn();
			".$raggruppamentoOrderAction['controller']." = true;
		}
	});";
} // foreach($raggruppamentoOrderActions as $raggruppamentoOrderAction)
$js .= "});";

$this->Html->scriptBlock($js, ['block' => true]);
?>


<style>.popupHelpOnline {
 background:#666
}
a.helpOnlineBack {
 color:#fff;
 cursor:pointer;
 font-size:18px;
 font-weight:700;
 text-decoration:none
}
ul.helpOnline {
 list-style:none;
 margin:10px 0 0
}
ul.helpOnline a {
 background:#fff;
 color:#333;
 display:inline;
 float:left;
 font-size:18px;
 margin:0 0 27px 30px;
 padding:10px 10px 15px;
 text-align:center;
 text-decoration:none;
 transform:rotate(-2deg);
 width:auto
}
.cake-sql-log caption,
.popupNum {
 color:#fff
}
ul.helpOnline img {
 display:block;
 margin-bottom:12px;
 width:190px
}
ul.helpOnline a:after {
 content:attr(title)
}
ul.helpOnline li:nth-child(2n) a {
 transform:rotate(2deg)
}
ul.helpOnline li:nth-child(3n) a {
 position:relative;
 top:-5px;
 transform:none
}
ul.helpOnline li:nth-child(5n) a {
 position:relative;
 right:5px;
 transform:rotate(5deg)
}
ul.helpOnline li:nth-child(8n) a {
 position:relative;
 right:5px;
 top:8px
}
ul.helpOnline li:nth-child(11n) a {
 left:-5px;
 position:relative;
 top:3px
}
ul.helpOnline li a:hover {
 position:relative;
 transform:scale(1.25);
 z-index:5
}
</style>