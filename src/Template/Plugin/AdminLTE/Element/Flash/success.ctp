<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<section class="content-header">
    <div class="alert alert-success alert-dismissible">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <h4><i class="icon fa fa-check"></i> <?= __('Flash-Message') ?>!</h4>
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