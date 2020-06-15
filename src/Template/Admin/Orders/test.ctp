<?php

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($scope);
// debug($htmlCustomSiteOrders);
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Order
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
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
          <?php echo $this->Form->create($order, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                // echo $this->HtmlCustomSite->boxSupplierOrganization($suppliersOrganizations);
                echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);
                
                echo $this->{$htmlCustomSiteOrders}->deliveries($deliveries);

                echo '<div class="row">';
                echo '<div class="col-md-6">'; 
                echo $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
                echo '</div>'; 
                echo '<div class="col-md-6">'; 
                echo $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
                echo '</div>'; 

                echo $this->Form->control('nota');
                echo $this->Form->control('hasTrasport');
                echo $this->Form->control('trasport_type');
                echo $this->Form->control('trasport');
                echo $this->Form->control('hasCostMore');
                echo $this->Form->control('cost_more_type');
                echo $this->Form->control('cost_more');
                echo $this->Form->control('hasCostLess');
                echo $this->Form->control('cost_less_type');
                echo $this->Form->control('cost_less');

                echo $this->Form->control('qta_massima');
                echo $this->Form->control('qta_massima_um');
                echo $this->Form->control('importo_massimo');
              ?>
            </div>
            <!-- /.box-body -->

          <?php echo $this->Form->submit(__('Submit')); ?>

          <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
