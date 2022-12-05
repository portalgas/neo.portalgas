<?php
use Cake\Core\Configure;
// echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Orders'), 'subtitle' => 'aggiungi']);

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
// debug($htmlCustomSiteOrders);
?>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Order-'.$order_type_id); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          if(!empty($suppliersOrganizations) && !empty($deliveries)) {
            echo $this->Form->create($order, ['role' => 'form']);
            echo '<div class="box-body">';

                /*
                 * passato per OrderValidation
                 */
                echo $this->{$htmlCustomSiteOrders}->hiddenFields($this->Identity->get()->organization->id, $parent);
                
                echo $this->{$htmlCustomSiteOrders}->infoParent($parent);

                /*
                 * produttore
                 */
                echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);
                
                echo $this->{$htmlCustomSiteOrders}->deliveries($deliveries, $deliveryOptions);

                echo $this->{$htmlCustomSiteOrders}->data($parent);

                echo $this->{$htmlCustomSiteOrders}->note();

                echo $this->{$htmlCustomSiteOrders}->mailOpenTesto();

                echo $this->{$htmlCustomSiteOrders}->extra($order, $parent);
               
            echo '</div>';  // /.box-body 

            echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

          echo $this->Form->end(); 
          } // end if(!empty($suppliersOrganizations) && !empty($deliveries))
          
          echo '</div>';  // /.box -->
      echo '</div>';
  echo '</div>';  //  /.row -->
echo '</section>'; 