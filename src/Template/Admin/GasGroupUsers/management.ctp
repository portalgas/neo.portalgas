<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GasGroupUser $gasGroupUser
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Gas Group Users Management'); ?>
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
            <h3 class="box-title"><?php echo 'Associa gasisti al gruppo '.$gasGroup->name; ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          
          echo $this->Form->create($gasGroup, ['role' => 'form', 'id' => 'frm']);
          echo $this->Form->hidden('gas_group_id', ['value' => $gas_group_id]);
          
          echo '<div class="box-body">'; 

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('to_user_id', ['id' => 'to_user_id', 'label' => 'Tutti i gasisti', 'options' => $users, 'multiple' => true, 'size' => 10]);
          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('user_id', ['id' => 'user_id', 'label' => 'Tutti i gasisti del gruppo', 'options' => $gasGroupUsers, 'multiple' => true, 'size' => 10]);
          echo $this->Form->hidden('user_ids', ['id' => 'user_ids', 'value' => '']);
          echo '</div>';
          echo '</div>'; // row
          echo '</div>';
          echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);
          echo $this->Form->end();
          
        echo '</div>';

        echo $this->element('msg', ['class' => 'info', 'msg' => 'Gruppo creato da "<b>'.$gasGroup->user->name.'</b>": l\'utente farà parte di default del gruppo']);
        ?>
      </div>
  </div>
  <!-- /.row -->
</section>

<?php 
$js = "
$(function () {
	$('#to_user_id').click(function() {
		$('#to_user_id option:selected').each(function (){			
			$('#user_id').append($('<option></option>')
	         .attr('value',$(this).val())
	         .text($(this).text()));
	         
	         $(this).remove();
		});
	});
	
	$('#user_id').click(function() {
		$('#user_id option:selected').each(function (){			
			$('#to_user_id').append($('<option></option>')
	         .attr('value',$(this).val())
	         .text($(this).text()));
	         
	         $(this).remove();
		});
	});

	$('#frm').submit(function() {

      var user_ids = '';
      $('#user_id option').each(function () {	
        user_ids +=  $(this).val()+',';
      });
      user_ids = user_ids.substring(0,user_ids.length-1);
      
      if(user_ids=='') {
        alert('Devi selezionare almeno un utente da associare al gruppo');
        return false;
      }
      
      $('#user_ids').val(user_ids);			
  
		return true;			
	});

});
";

$this->Html->scriptBlock($js, ['block' => true]);
?>
