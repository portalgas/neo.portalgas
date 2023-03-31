<?php
use Cake\Core\Configure;

$user = $this->Identity->get();
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id, $user, $parent, $order);
// debug($htmlCustomSiteOrders);

echo $this->Html->script('ordersForm', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order-'.$order_type_id), 'subtitle' => __('Add')], ['home', 'list-orders']);
?>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        
        <?php 
          echo $htmlCustomSiteOrders->infoParent();
        ?>

        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Dati ordine'); ?></h3>
          </div>          
          <?php 
            echo $this->Form->create($order, ['role' => 'form', 'id' => 'frm']);
            echo '<div class="box-body" style="padding: 25px;">';

                /*
                 * passato per OrderValidation
                 */
                echo $htmlCustomSiteOrders->hiddenFields();
                
                /*
                 * produttore + ricava i ruoli DES dello user x modal
                 */
                echo $htmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations);
                
                $deliveries = $htmlCustomSiteOrders->deliveries($deliveries, $deliveryOptions);
                echo $deliveries['html'];
                if(isset($deliveries['bottom'])) { // html inserito nel Layout in fondo, ex modal
                  $this->start('bottom');
                    echo $deliveries['bottom'];
                  $this->end();
                }

                echo $htmlCustomSiteOrders->data();

                /*
                 * gli ordini titolari per il gruppo sono ordini che non appaiono a FE
                 * e non hanno articoli acquistabili ma solo ereditari dagli ordini gruppo
                 */
                if($order_type_id!=Configure::read('Order.type.gas_parent_groups')) {

                  echo $htmlCustomSiteOrders->note();

                  echo $htmlCustomSiteOrders->mailOpenTesto();

                  echo $htmlCustomSiteOrders->monitoraggio();
                
                  echo $htmlCustomSiteOrders->typeGest();
                
                  echo $htmlCustomSiteOrders->extra();
                } // if($order_type_id!=Configure::read('Order.type.gas_parent_groups'))
               
            echo '</div>';  // /.box-body 

            echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

          echo $this->Form->end(); 
          
          echo '</div>';  // /.box -->
      echo '</div>';
  echo '</div>';  //  /.row -->
echo '</section>'; 