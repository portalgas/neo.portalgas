<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="<?php echo $totResults;?>" class="badge bg-light-blue"><?php echo $totResults;?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Mapping']) ?></legend>
    <?php
    echo '<div class="row-">';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_queue_id', ['options' => $queues, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_master_scope_id', ['options' => $master_scopes, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_master_table_id', ['options' => $master_tables, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '</div>';


    echo '<div class="row-">';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_mapping_type_id', ['options' => $mapping_types, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_slave_scope_id', ['options' => $slave_scopes, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_slave_table_id', ['options' => $slave_tables, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '</div>';
    ?>
</fieldset>
</div>
    <div class="box-footer">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>
</div>
<?= $this->Form->end() ?>