<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $kDelivery
 */
?>
<?php
use Cake\Core\Configure;
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Gas Group Delivery'); ?>
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build('/'); ?>"><i class="fa fa-home"></i> <?php echo __('Home'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-list"></i> <?php echo __('List'); ?></a></li>
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
          <?php 
          echo $this->Form->create($gasGroupDelivery, ['role' => 'form']);
          echo '<div class="box-body">';


 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('gas_group_id', ['options' => $gasGroups, 'required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('luogo', ['required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->HtmlCustom->datepicker('data', ['autocomplete' => 'off', 'required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-6">';
echo $this->Form->control('orario_da', ['type' => 'time', 'interval' => 15, 'class' => 'form-control', 'required' => 'required']);
echo '</div>';
echo '<div class="col-md-6">';
echo $this->Form->control('orario_a', ['type' => 'time', 'interval' => 15, 'class' => 'form-control', 'required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('nota', ['type' => 'textarea']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('nota_evidenza', ['options' => $nota_evidenzas]);
echo '</div>';
echo '</div>'; // row
      
          echo $this->Form->button(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
          echo '</div>'; /* .box-body */
          echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>