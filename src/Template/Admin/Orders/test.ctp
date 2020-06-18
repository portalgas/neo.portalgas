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
          <?php 
            echo $this->Form->create($order, ['role' => 'form']); 
            echo '<div class="box-body">';
                /*
                 * passato per OrderValidation
                 */
                echo $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $this->Identity->get()->organization->id, 'required' => 'required']);
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
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-12">'; 
                echo $this->Form->control('nota');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('hasTrasport');
                echo '</div>'; 
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('trasport_type');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('trasport');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('hasCostMore');
                echo '</div>'; 
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('cost_more_type');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('cost_more');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('hasCostLess');
                echo '</div>'; 
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('cost_less_type');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('cost_less');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('qta_massima');
                echo '</div>'; 
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('qta_massima_um');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('importo_massimo');
                echo '</div>'; 
                echo '</div>'; 
      
           echo '</div>'; // <!-- /.box-body -->

           echo $this->Form->submit(__('Submit')); 

          echo $this->Form->end(); 
        echo '</div>';  // <!-- /.box -->
      echo '</div>';
  echo '</div>'; //  <!-- /.row -->
echo '</section>';