<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KOrder $order
 */
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

                /*
                 * passato per OrderValidation
                 */
                echo $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $this->Identity->get()->organization->id, 'required' => 'required']);

                echo $this->Form->control('supplier_organization_id', ['options' => $suppliersOrganizations]);
                echo $this->Form->control('owner_articles');
                echo $this->Form->control('owner_organization_id', ['options' => $ownerOrganizations]);
                echo $this->Form->control('owner_supplier_organization_id', ['options' => $ownerSupplierOrganizations]);
                echo $this->Form->control('delivery_id', ['options' => $deliveries]);
                echo $this->Form->control('prod_gas_promotion_id');
                echo $this->Form->control('des_order_id');
                echo $this->Form->control('data_inizio');
                echo $this->Form->control('data_fine');
                echo $this->Form->control('data_fine_validation');
                echo $this->Form->control('data_incoming_order');
                echo $this->Form->control('data_state_code_close');
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
                echo $this->Form->control('typeGest');
                echo $this->Form->control('state_code', ['value' => 'CREATE-INCOMPLETE']);
                echo $this->Form->control('mail_open_send');
                echo $this->Form->control('mail_open_data');
                echo $this->Form->control('mail_close_data');
                echo $this->Form->control('mail_open_testo');
                echo $this->Form->control('type_draw');
                echo $this->Form->control('tot_importo', ['value' => 0]);
                echo $this->Form->control('qta_massima', ['value' => 0]);
                echo $this->Form->control('qta_massima_um');
                echo $this->Form->control('send_mail_qta_massima');
                echo $this->Form->control('importo_massimo', ['value' => 0]);
                echo $this->Form->control('send_mail_importo_massimo');
                echo $this->Form->control('tesoriere_nota');
                echo $this->Form->control('tesoriere_fattura_importo', ['value' => 0]);
                echo $this->Form->control('tesoriere_doc1');
                echo $this->Form->control('tesoriere_data_pay');
                echo $this->Form->control('tesoriere_importo_pay', ['value' => 0]);
                echo $this->Form->control('tesoriere_stato_pay');
                echo $this->Form->control('inviato_al_tesoriere_da');
                echo $this->Form->control('isVisibleFrontEnd');
                echo $this->Form->control('isVisibleBacoffice', ['value' => 'Y']);
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
