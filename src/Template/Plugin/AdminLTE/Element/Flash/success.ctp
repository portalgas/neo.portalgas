<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}

// debug($message);
?>
<section class="content-header">
    <div class="alert alert-success alert-dismissible">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <h4><i class="icon fa fa-check"></i> <?= __('Flash-Message') ?>!</h4>
        <?php
        if(is_array($message)) {
            foreach($message as $key => $values) {
                echo '<div class="row">';
                echo '<div class="col-md-2">'.__($key).'</div>';
                echo '<div class="col-md-10">';
                echo '<ul>';
                foreach($values as $key => $value) {
                    echo '<li>';
                    echo $value;
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }
        }
        else
            echo $message;
        ?>
    </div>
</section>