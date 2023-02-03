<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/orderPriceTypes', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/suppliersOrganization', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Orders'), 'subtitle' => 'test']);

echo $this->element('msg', ['msg' => 'testing pact']);

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id);
$this->{$htmlCustomSiteOrders}->setUser($this->Identity->get());
// debug($htmlCustomSiteOrders);
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Order
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


                /* per test
                * echo $this->Form->control('id', ['type' => 'hidden', 'id' => 'order_id', 'value' => 20161]);
                */
                
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

                echo $this->HtmlCustomSite->orderPriceTypes($price_type_enums);
/*
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
                echo $this->Form->control('qta_massima', ['value' => 0]);
                echo '</div>'; 
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('qta_massima_um');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('importo_massimo', ['value' => 0]);
                echo '</div>'; 
                echo '</div>'; 
      
               // echo $this->Form->control('state_code', ['value' => 'CREATE-INCOMPLETE']);
                echo $this->Form->control('tot_importo', ['value' => 0]);
                echo $this->Form->control('tesoriere_fattura_importo', ['value' => 0]);
                echo $this->Form->control('tesoriere_importo_pay', ['value' => 0]);
                echo $this->Form->control('isVisibleBacoffice', ['value' => 'Y']);
*/
           echo '</div>'; // <!-- /.box-body -->

           echo $this->Form->submit(__('Submit')); 

          echo $this->Form->end(); 
        echo '</div>';  // <!-- /.box -->
      echo '</div>';
  echo '</div>'; //  <!-- /.row -->
echo '</section>';

$js = "var json_price_types = ".$json_price_types;
$this->Html->scriptBlock($js, ['block' => true]);