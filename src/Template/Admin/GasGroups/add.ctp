<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __('Gas Group'), 'subtitle' => __('Add')], ['home', 'list']);
?>
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
          <?php echo $this->Form->create($gasGroup, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('name');
                echo $this->Form->control('descri');
              ?>
            </div>
            <!-- /.box-body -->

          <?php 
          echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
          echo $this->Form->end(); 
          ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
