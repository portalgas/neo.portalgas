<?php
use Cake\Core\Configure;
// echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order-'.$order_type_id), 'subtitle' => __('Edit')], ['home', 'list']);
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
$this->{$htmlCustomSiteOrders}->setUser($this->Identity->get());
// debug($htmlCustomSiteOrders);
?>
  <section class="content">
    <div class="row">
      <div class="col-md-12">

        <?php 
          echo $this->{$htmlCustomSiteOrders}->infoParent($parent);
        ?>
        
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Dati ordine'); ?></h3>
          </div>          
          <?php 
            echo $this->Form->create($order, ['role' => 'form']);
            echo '<div class="box-body">';

                /*
                 * passato per OrderValidation
                 */
                echo $this->{$htmlCustomSiteOrders}->hiddenFields($this->Identity->get()->organization->id, $parent);

                /*
                 * produttore
                 */
                echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);
                
                echo $this->{$htmlCustomSiteOrders}->deliveries($deliveries, $deliveryOptions);

                echo $this->{$htmlCustomSiteOrders}->data($parent);

                echo $this->{$htmlCustomSiteOrders}->note();

                echo $this->{$htmlCustomSiteOrders}->mailOpenTesto();

                echo $this->{$htmlCustomSiteOrders}->monitoraggio($order);

                echo $this->{$htmlCustomSiteOrders}->typeGest($order);
                
                echo $this->{$htmlCustomSiteOrders}->extra($order, $parent);
               
            echo '</div>';  // /.box-body 

            echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

          echo $this->Form->end(); 

          echo '</div>';  // /.box -->
      echo '</div>';
  echo '</div>';  //  /.row -->
echo '</section>'; 