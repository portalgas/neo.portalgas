<?php
use Cake\Core\Configure;

echo $this->Html->script('mapping', ['block' => 'scriptPageInclude']);
?>
  <section class="content-header">
    <h1>
      Mapping
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
          <?php echo $this->Form->create($mapping, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('queue_id', ['type' => 'hidden','value' => $queue->id]);

                echo $this->Form->control('queue_id_hidden', ['disabled' => 'disabled','value' => $queue->name.' - '.$queue->queue_mapping_type->name.' '.$queue->queue_mapping_type->descri]);

                echo $this->Form->control('name');

                echo $this->Form->control('sort', ['value' => $sort]);
                echo $this->Form->control('descri');

                switch (strtoupper($queue->queue_mapping_type->code)) {
                  case 'DB':
                    echo '<div class="row">';
                    echo '<div class="col-md-4">'; 
                    echo $this->Form->control('master_scope_id', ['options' => $master_scopes, 'class' => 'form-control select2']);
                    echo '</div>';
                    echo '<div class="col-md-4">';     
                    echo $this->Form->control('master_table_id', ['options' => $master_tables, 'class' => 'form-control select2', 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty')]);
                    echo '</div>';
                    echo '<div class="col-md-4">';     
                    echo $this->Form->control('master_column');
                    echo '</div>';
                    echo '</div>';
                  break;
                  case 'XML':
                    echo '<div class="row">';
                    echo '<div class="col-md-4">'; 
                    echo $this->Form->control('master_scope_id', ['options' => $master_scopes, 'class' => 'form-control select2']);
                    echo '</div>';
                    echo '<div class="col-md-8">';  
                    echo $this->Form->control('master_xml_xpath');
                    echo '</div>';
                    echo '</div>';
                  break;
                  case 'CSV':
                    echo '<div class="row">';
                    echo '<div class="col-md-4">'; 
                    echo $this->Form->control('master_scope_id', ['options' => $master_scopes, 'class' => 'form-control select2']);
                    echo '</div>';
                    echo '<div class="col-md-8">';  
                    echo $this->Form->control('master_csv_num_col');
                    echo '</div>';
                    echo '</div>'; 
                  break;
                }

                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('slave_scope_id', ['options' => $slaveScopes, 'class' => 'form-control select2']);
                echo '</div>';
                echo '<div class="col-md-4">';     
                echo $this->Form->control('slave_table_id', ['options' => $slaveTables, 'class' => 'form-control select2']);
                echo '</div>';
                echo '<div class="col-md-4">';     
                echo $this->Form->control('slave_column');
                echo '</div>';
                echo '</div>';


                echo '<div class="row">';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('mapping_type_id', ['options' => $mapping_types, 'class' => 'form-control select2']);
                echo '</div>';
                
                echo '<div class="col-md-8">';
                // if(strtoupper($queue->queue_mapping_type->code=='DB')) {
                  echo '<div class="box-mapping-type-id-inner-table-parent">'; 
                  echo $this->Form->control('queue_table_id', ['options' => $queue_tables, 'empty' => Configure::read('HtmlOptionEmpty')]);
                  echo $this->HtmlCustom->note(__('note_mapping_queue_table'));                  
                //} 
                echo '</div>';
                echo '</div>';
                echo '</div>';

                echo '<div class="row">';
                echo '<div class="col-md-6">';
                echo '<div class="box-mapping-type-id-value">';
                echo $this->Form->control('value');
                echo '</div>';
                echo '</div>';
                echo '<div class="col-md-6">'; 
                echo $this->Form->control('mapping_value_type_id', ['options' => $mapping_value_types, 'class' => 'form-control select2', 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty')]);
                echo $this->HtmlCustom->note(__('note_function_not_implement'));
                echo '</div>';
                echo '</div>';

                echo $this->Form->control('parameters');
                echo $this->HtmlCustom->note(__('note_function_not_implement'));

                echo '<div class="row">';
                echo '<div class="col-md-4">';
                echo $this->Form->control('is_required');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('value_default');
                echo '</div>';
                echo '<div class="col-md-4">'; 
                echo $this->Form->control('is_active');
                echo '</div>';
                echo '</div>';

           
           echo '</div>';

            echo $this->Form->radio('reAdd', $reAdds, ['default' => $reAdd]);
             
            echo '</div>';
            
            echo $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary pull-right']); ?>


          <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
