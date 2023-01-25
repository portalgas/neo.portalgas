<?php 
use Cake\Core\Configure;

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
// debug($htmlCustomSiteOrders);
?>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
      <span data-toggle="tooltip" title="totale ordini <?php echo $orders->count();?>" class="badge bg-light-blue"><?php echo $orders->count();?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Order']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-5">';
    $options = [];
    $options['ctrlDesACL'] = false;
    $options['id'] = 'search_supplier_organization_id'; // non c'e' il bind in supplierOrganization.js
    $options['default'] = $search_supplier_organization_id;
    echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations, $options);
    echo '</div>';
    echo '<div class="col-md-5">';
    echo $this->Form->control('order_delivery_date', ['label' => __('Sort'), 'options' => $order_delivery_dates, 'default' => $order_delivery_date]);
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