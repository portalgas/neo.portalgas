<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KDesOrdersOrganization $kDesOrdersOrganization
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Des Orders Organization
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
          <?php echo $this->Form->create($kDesOrdersOrganization, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('des_id');
                echo $this->Form->control('des_order_id', ['options' => $desOrders]);
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('order_id', ['options' => $orders]);
                echo $this->Form->control('luogo');
                echo $this->Form->control('data');
                echo $this->Form->control('orario');
                echo $this->Form->control('contatto_nominativo');
                echo $this->Form->control('contatto_telefono');
                echo $this->Form->control('contatto_mail');
                echo $this->Form->control('nota');
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
