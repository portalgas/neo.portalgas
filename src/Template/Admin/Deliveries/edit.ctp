<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KDelivery $delivery
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Delivery
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
          <?php echo $this->Form->create($delivery, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('luogo');
                echo $this->Form->control('data');
                echo $this->Form->control('orario_da');
                echo $this->Form->control('orario_a');
                echo $this->Form->control('nota');
                echo $this->Form->control('nota_evidenza');
                echo $this->Form->control('isToStoreroom');
                echo $this->Form->control('isToStoreroomPay');
                echo $this->Form->control('stato_elaborazione');
                echo $this->Form->control('isVisibleFrontEnd');
                echo $this->Form->control('isVisibleBackOffice');
                echo $this->Form->control('sys');
                echo $this->Form->control('gcalendar_event_id');
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
