<?php
$tmpl = 2; // ALL

if(empty($class)) {
	$class = 'danger';
	$class2 = 'bg-red';
}
if(empty($class2)) 
	$class2 = 'bg-red';


if(empty($msg_header))
	$msg_header = __('Results');

if(empty($msg))
	$msg = __('MsgResultsNotFound');

if(!isset($action_add) || (empty($action_add) && $action_add!==false))
	$action_add = true;

if(empty($url_add))
	$url_add = $this->Url->build(['controller' => $this->request->getParam('controller'), 'action' => 'add']);	
?>

<?php
if($tmpl==1 || $tmpl=='ALL') {
?>
	<div class="callout callout-<?php echo $class;?>" style="clear: both;">
	  <h4><?php echo $msg_header;?></h4>

	  <p><?php echo $msg;?></p>
	</div>
<?php
}

if($tmpl==2 || $tmpl=='ALL') {
?>
	<div class="info-box" style="clear: both;">
		<span class="info-box-icon <?php echo $class2;?>"><i class="fa fa-ban"></i></span>

		<div class="info-box-content">
		  <span class="info-box-text"><?php echo $msg_header;?></span>
		  <span class="info-box-number"><?php echo $msg;?></span>
		  <?php
		  if($action_add)
		  	echo '<a href="'.$url_add.'" class="small-box-footer">'.__('Add').' <i class="fa fa-arrow-circle-right"></i></a>';
		  ?>
		</div>
	</div>
<?php
}

if($tmpl==3 || $tmpl=='ALL') {
?>
<div class="small-box <?php echo $class2;?>">
	<div class="inner">
  		<h3><?php echo $msg_header;?></h3>
	    <p><?php echo $msg;?></p>
	</div>
	<div class="icon">
	  <i class="ion ion-pie-graph"></i>
	</div>
	  <?php
	  if($action_add)
	  	echo '<a href="'.$url_add.'" class="small-box-footer">'.__('Add').' <i class="fa fa-arrow-circle-right"></i></a>';
	  ?>
</div>
<?php
}
?>
