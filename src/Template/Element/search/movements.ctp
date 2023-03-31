<?php 
use Cake\Core\Configure;

$user = $this->Identity->get();
?>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
      <span data-toggle="tooltip" title="totale ordini <?php echo count($movements);?>" class="badge bg-light-blue"><?php echo count($movements);?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Movement']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-5">';
    echo $this->Form->control('search_movement_type_id', ['label' => __('Type'), 'options' => $movement_types, 'default' => $search_movement_type_id, 'empty' => Configure::read('HtmlOptionEmpty')]);
    echo '</div>';
    echo '<div class="col-md-5">';
    echo $this->Form->control('search_year', ['label' => __('Year'), 'options' => $years, 'default' => $search_year]);
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