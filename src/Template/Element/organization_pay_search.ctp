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
    <legend><?= __('Search {0}', ['OrganizationPay']) ?></legend>
    <?php
    if(!empty($search_year)) {
        echo '<div class="row-">';
        echo '<div class="col-md-12">';    
        echo $this->Form->control('search_year', ['options' => $years, 'class' => 'form-control', 'default' => $search_year]);
        echo '</div>';
        echo '</div>';    
    }

    echo '<div class="row-">';
    echo '<div class="col-md-3">';    
    echo $this->Form->control('search_beneficiario_pay', ['options' => $beneficiario_pays, 'default' => $search_beneficiario_pay, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-2">';    
    echo $this->Form->control('search_hasMsg', ['options' => $hasMsgs, 'default' => $search_hasMsg, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')], 'label' => __('Msg attivato')]);
    echo '</div>';
    echo '<div class="col-md-3">';    
    echo $this->Form->control('search_organization_id', ['options' => $organizations, 'default' => $search_organization_id, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-2">';    
    echo $this->Form->control('search_type_pay', ['options' => $type_pays, 'default' => $search_type_pay, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-2">';    
    echo $this->Form->control('search_hasPdf', ['options' => $hasPdfs, 'default' => $search_hasPdf, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')], 'label' => __('Pdf')]);
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