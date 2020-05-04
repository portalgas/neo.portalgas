<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KStatOrder $kStatOrder
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Stat Order
      <small><?php echo __('Edit'); ?></small>
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
          <?php echo $this->Form->create($kStatOrder, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('supplier_organization_id');
                echo $this->Form->control('supplier_organization_name');
                echo $this->Form->control('supplier_img1');
                echo $this->Form->control('stat_delivery_id');
                echo $this->Form->control('stat_delivery_year');
                echo $this->Form->control('data_inizio', ['empty' => true]);
                echo $this->Form->control('data_fine', ['empty' => true]);
                echo $this->Form->control('importo');
                echo $this->Form->control('tesoriere_fattura_importo');
                echo $this->Form->control('tesoriere_doc1');
                echo $this->Form->control('tesoriere_data_pay', ['empty' => true]);
                echo $this->Form->control('tesoriere_importo_pay');
                echo $this->Form->control('request_payment_num');
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
