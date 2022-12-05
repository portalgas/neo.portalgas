<?php
$tmpl = 1; // ALL

if(empty($class)) 
	$class = 'success'; // warning success danger info

if(empty($msg_header))
	$msg_header = __('Note');

if(empty($msg))
	$msg = __('MsgResultsNotFound');

if($tmpl==1 || $tmpl=='ALL') {
	echo '<div class="callout callout-'.$class.'" style="clear: both;margin: 25px">';
	echo '<h4 class="msg-header">'.$msg_header.'</h4>';
	echo '<p class="msg-body">'.$msg.'</p>';
	echo '</div>';
}
?>