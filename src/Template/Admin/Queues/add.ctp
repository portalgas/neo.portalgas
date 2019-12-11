<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Queue $queue
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Queue
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
          <?php echo $this->Form->create($queue, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo '<div class="row">';
                echo '<div class="col-md-4">';
                echo $this->Form->control('code');
                echo $this->HtmlCustom->note(__('note_uppercase'));
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('name');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('component');
                echo $this->HtmlCustom->note(__('note_queue_component'));
                echo '</div>';
                echo '</div>';
                

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('queue_mapping_type_id', ['options' => $queueMappingTypes, 'class' => 'form-control select2']);
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('db_master_datasource');
                echo $this->HtmlCustom->note(__('note_queue_db_datasource_override'));
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('db_slave_datasource');
                echo $this->HtmlCustom->note(__('note_queue_db_datasource_override'));
                echo '</div>';
                echo '</div>';


                echo '<div class="row">';
                echo '<div class="col-md-6">'; 
                echo $this->Form->control('master_scope_id', ['options' => $master_scopes, 'class' => 'form-control select2']);
                echo '</div>';
                echo '<div class="col-md-6">';     
                echo $this->Form->control('slave_scope_id', ['options' => $slave_scopes, 'class' => 'form-control select2']);
                echo '</div>';
                echo '</div>';


                echo $this->Form->control('descri');
                
                echo $this->Form->control('is_system');
                echo $this->Form->control('is_active');
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
