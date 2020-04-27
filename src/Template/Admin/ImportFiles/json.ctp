  <section class="content-header">
    <h1>
      Import File
      <small><?php echo __('Json'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
  </section>


<?php
/* 
* errors
*/
if(isset($errors)) {

  if($application_env='development') debug($errors);

    echo '<section class="content">';
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="box box-primary">';
    echo '<div class="box-body">';

    if(isset($errors['error']))
    foreach($errors['error'] as $field => $error) {
        echo __($field).' ';
        foreach($error as $err) {
            echo $err.' ';
        }
        echo '<br />';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}
?>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Import File Json'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(null, ['role' => 'form', 'type' => 'file']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('file', ['type' => 'file']);
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
