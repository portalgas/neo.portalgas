<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="<?= h($class) ?>" onclick="this.classList.add('hidden');">
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