<?php
use Cake\Core\Configure;
// echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order-'.$order_type_id), 'subtitle' => __('Add')], ['home', 'list']);
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
// debug($htmlCustomSiteOrders);
?>

  <section class="content-header">
    <h1>
      <?php echo __('Loops Order'); ?>
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build('/'); ?>"><i class="fa fa-home"></i> <?php echo __('Home'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['action' => 'view']); ?>"><i class="fa fa-eye"></i> <?php echo __('View'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-list"></i> <?php echo __('List'); ?></a></li>
    </ol>
  </section>

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
echo $this->Form->control('gg_data_inizio');
echo '</div>';
echo '<div class="col-md-6">';
echo $this->Form->control('gg_data_fine');
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('flag_send_mail');
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
