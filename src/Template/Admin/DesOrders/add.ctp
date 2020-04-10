<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KDesOrder $kDesOrder
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Des Order
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
          <?php echo $this->Form->create($kDesOrder, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('des_id');
                echo $this->Form->control('des_supplier_id');
                echo $this->Form->control('luogo');
                echo $this->Form->control('nota');
                echo $this->Form->control('nota_evidenza');
                echo $this->Form->control('data_fine_max');
                echo $this->Form->control('hasTrasport');
                echo $this->Form->control('trasport');
                echo $this->Form->control('hasCostMore');
                echo $this->Form->control('cost_more');
                echo $this->Form->control('hasCostLess');
                echo $this->Form->control('cost_less');
                echo $this->Form->control('state_code');
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('order_id', ['options' => $orders]);
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
