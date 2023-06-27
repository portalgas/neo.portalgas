<?php 
use Cake\Core\Configure;

$user = $this->Identity->get();
?>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
      <span data-toggle="tooltip" title="totale ordini <?php echo count($articles);?>" class="badge bg-light-blue"><?php echo count($articles);?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Articles']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-3">';
    $options = [];
    $options['ctrlDesACL'] = false;
    $options['id'] = 'search_supplier_organization_id'; // non c'e' il bind in supplierOrganization.js
    $options['default'] = $search_supplier_organization_id;
    $options['empty'] = true;
    echo $this->HtmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations, $options);
    echo '</div>';
    echo '<div class="col-md-4">';
    echo $this->Form->control('search_name', ['label' => __('Name'), 'value' => $search_name, 'placeholder' => __('Name')]);
    echo '</div>';
    echo '<div class="col-md-3">';
    echo $this->Form->control('search_code', ['label' => __('Code'), 'value' => $search_code, 'placeholder' => __('Code')]);
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