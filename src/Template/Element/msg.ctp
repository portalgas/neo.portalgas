<?php
$tmpl = 1; // ALL

if(empty($class)) 
	$class = 'success'; // warning success danger info

if(empty($msg_header))
	$msg_header = __('Note');

if(empty($msg))
	$msg = __('MsgResultsNotFound');
?>

<?php
if($tmpl==1 || $tmpl=='ALL') {
	echo '<div class="callout callout-'.$class.'" style="clear: both;">';
	echo '<h4>'.$msg_header.'</h4>';
	echo '<p>'.$msg.'</p>';
	echo '</div>';
}
?>