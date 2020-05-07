<?php
$tmpl = 2; // ALL

if(empty($class)) {
	$class = 'info';
}
$class2 = 'bg-aqua';
if(empty($msg_header))
	$msg_header = __('headerIsSearch');

if(empty($msg))
	$msg = sprintf(__('msgIsSearch'), __(ucfirst($field)), $q);

?>

<?php
if($tmpl==1 || $tmpl=='ALL') {
?>
	<div class="callout callout-<?php echo $class;?>" style="clear: both;">
	  <h4><i class="icon fa fa-info"></i> <?php echo $msg_header;?></h4>

	  <p><?php echo $msg;?></p>
	</div>
<?php
}

if($tmpl==2 || $tmpl=='ALL') {
?>
	<div class="info-box" style="clear: both;">
		<span class="info-box-icon <?php echo $class2;?>"><i class="fa fa-search"></i></span>

		<div class="info-box-content">
		  <span class="info-box-text"><?php echo $msg_header;?></span>
		  <span class="info-box-number"><?php echo $msg;?></span>
		</div>
	</div>
<?php
}