<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<section class="content-header">
    <div class="alert alert-danger alert-dismissible">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <h4><i class="icon fa fa-ban"></i> <?= __('Flash-Alert') ?>!</h4>
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
</section>
