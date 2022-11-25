<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GasGroupUser $gasGroupUser
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Gas Group Users'); ?>
      <small><?php echo __('Management'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['controller' => 'GasGroups', 'action' => 'index']); ?>"><i class="fa fa-users"></i> <?php echo __('Gas Groups'); ?></a></li>
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
          echo $this->Form->create($gasGroupUser, ['role' => 'form']); 
          echo $this->Form->hidden('gas_group_id');
          
          echo '<div class="box-body">'; 

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('to_user_ids', ['id' => 'to_user_ids', 'label' => 'Tutti i gasisti', 'options' => $users, 'multiple' => true, 'size' => 10]);
          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('user_ids', ['id' => 'user_ids', 'label' => 'Tutti i gasisti del gruppo', 'options' => $gasGroupUsers, 'multiple' => true, 'size' => 10]);
          echo '</div>';
          echo '</div>'; // row
          echo '</div>';
          echo $this->Form->submit(__('Submit')); 
          echo $this->Form->end();
          ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>

<?php 
$js = "
$(function () {
	$('#to_user_ids').click(function() {
		$('#to_user_ids option:selected').each(function (){			
			$('#user_ids').append($('<option></option>')
	         .attr('value',$(this).val())
	         .text($(this).text()));
	         
	         $(this).remove();
		});
	});
	
	$('#user_ids').click(function() {
		$('#user_ids option:selected').each(function (){			
			$('#to_user_ids').append($('<option></option>')
	         .attr('value',$(this).val())
	         .text($(this).text()));
	         
	         $(this).remove();
		});
	});
});
";

$this->Html->scriptBlock($js, ['block' => true]);
?>
