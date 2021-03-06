<?php
use Cake\Core\Configure;

// echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/suppliersOrganization', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Orders'), 'subtitle' => 'aggiungi']);

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
// debug($htmlCustomSiteOrders);
?>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Dati ordine'); ?></h3>
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
                
                // debug($parents);
                echo $this->{$htmlCustomSiteOrders}->infoParent($parent);

                /*
                 * produttore
                 */
                  echo '<div class="row">';
                  echo '<div class="col-md-8">';
                  // echo $this->HtmlCustomSite->boxSupplierOrganization($suppliersOrganizations);
                  echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);
                  echo '</div>';
                  echo '<div class="col-md-4" id="vue-supplier-organization" style="display: none;">';
                  echo '<div class="box-img" v-if="supplier_organization.supplier.img1!=\'\'"><img width="'.Configure::read('Supplier.img.preview.width').'" class="img-responsive-disabled userAvatar" v-bind:src="supplier_organization.img1" /></div>';
                  echo '<div class="box-name">{{supplier_organization.name}}</div>';
                  echo '<div class="box-owner">'.__('organization_owner_articles').': {{supplier_organization.owner_articles | ownerArticlesLabel}}</div>';
                  echo '</div>';
                  echo '</div>';

                  echo $this->{$htmlCustomSiteOrders}->deliveries($deliveries);

                  echo $this->{$htmlCustomSiteOrders}->data($parent);

                  echo $this->{$htmlCustomSiteOrders}->note();

                  echo $this->{$htmlCustomSiteOrders}->mailOpenTesto();

                  echo $this->{$htmlCustomSiteOrders}->costs($parent);
               
            echo '</div>';  // /.box-body 

            echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

          echo $this->Form->end(); 
          } // end if(!empty($suppliersOrganizations) && !empty($deliveries))
          
          echo '</div>';  // /.box -->
      echo '</div>';
  echo '</div>';  //  /.row -->
echo '</section>'; 