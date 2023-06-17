<?php 
use Cake\Core\Configure;

$user = $this->Identity->get();
?>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
      <span data-toggle="tooltip" title="totale gruppi <?php echo count($gasGroups);?>" class="badge bg-light-blue"><?php echo count($gasGroups);?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['GasGroup']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-5">';
    echo $this->Form->control('search_user_id', ['label' => __('User'), 'options' => $users, 'default' => $search_user_id, 'class' => 'select2 form-control', 'empty' => Configure::read('HtmlOptionEmpty')]);
    echo '</div>';
    echo '<div class="col-md-5">';
    echo '</div>';
    echo '<div class="col col-md-2 text-right">';
    echo '<br />';
    echo $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']);  
    echo '</div>';   
    echo '</div>';
    ?>
</fieldset>
</div>
    <!-- div class="box-footer">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div -->
</div>
<?= $this->Form->end() ?>