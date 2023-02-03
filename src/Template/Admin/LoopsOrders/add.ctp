<?php
use Cake\Core\Configure;
// echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Loops Order'), 'subtitle' => __('Add')], ['home', 'list']);
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
$this->{$htmlCustomSiteOrders}->setUser($this->Identity->get());
// debug($htmlCustomSiteOrders);
?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Form'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create($loopsOrder, ['role' => 'form']);
          echo '<div class="box-body">';


 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo '</div>';
echo '</div>'; // row
 
/*
  * produttore
  */
echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('loops_delivery_id');
echo '</div>';
echo '</div>'; // row
  
echo '<div class="row">';
echo '<div class="col-md-6">';
echo $this->Form->control('gg_data_inizio', ['type' => 'number', 'min' => 2, 'default' => 2]);
echo '</div>';
echo '<div class="col-md-6">';
echo $this->Form->control('gg_data_fine', ['type' => 'number', 'min' => 1, 'default' => 1]);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('is_active');
echo '</div>';
echo '</div>'; // row

echo $this->Form->button(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
echo '</div>'; /* .box-body */
echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
