<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MovementType $movementType
 */
?>
<?php
use Cake\Core\Configure;
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Movement Type'); ?>
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build('/'); ?>"><i class="fa fa-home"></i> <?php echo __('Home'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['action' => 'view']); ?>"><i class="fa fa-eye"></i> <?php echo __('View'); ?></a></li>
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
          echo $this->Form->create($movementType, ['role' => 'form']);
          echo '<div class="box-body">';


 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('name');
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('is_active');
echo $this->HtmlCustom->note(__('note_is_active'));
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('is_system');
echo $this->HtmlCustom->note(__('note_is_system'));
echo '</div>';
echo '</div>'; // row

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('sort');
echo '</div>';
echo '</div>'; // row

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->radio('model', $models);
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
