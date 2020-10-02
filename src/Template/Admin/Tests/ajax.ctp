  <section class="content-header">
    <h1>
      Price Type
      <small><?php echo __('Tests ajax'); ?></small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Tests ajax'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(null, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('service_url', ['options' => $service_urls]);
                echo $this->Form->control('delivery_id', ['options' => $deliveries]);
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
