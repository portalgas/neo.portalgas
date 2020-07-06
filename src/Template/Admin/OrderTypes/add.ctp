<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\OrderType $orderType
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Orders Type
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
          <?php echo $this->Form->create($orderType, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('code');
                echo $this->Form->control('name');
                echo $this->Form->control('descri');
                echo $this->Form->control('is_system');
                echo $this->Form->control('is_active');
                echo $this->Form->control('is_default_ini');
                echo $this->Form->control('is_default_end');
                echo $this->Form->control('sort');
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
