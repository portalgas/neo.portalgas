<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __('Gas Group User'), 'subtitle' => __('Add')], ['home', 'list']);
?>
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
          <?php echo $this->Form->create($gasGroupUser, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('user_id', ['options' => $users]);
                echo $this->Form->control('gas_group_id', ['options' => $gasGroups]);
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
