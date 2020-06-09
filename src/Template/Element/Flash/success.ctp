<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="message success" onclick="this.classList.add('hidden')">
<?php
if(is_array($message)) {
	foreach($message as $key => $values) {
		echo __($key).' ';
		foreach($values as $key => $value) 
			echo $value;

		echo '<br />';
	}
}
else
	echo $message;
?>
</div>