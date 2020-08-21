<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Document $document
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Document');?>
      <small><?php echo __('Add'); ?></small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Upload'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create($document, ['role' => 'form', 'type' => 'file']); 
          echo '<fieldset>';
          echo '<legend></legend>';

          echo '<div class="box-body">';

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('name');
          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('file_name', ['type' => 'file']);
          echo '</div>';
          echo '</div>';
                    
          echo $this->Form->control('descri');

          echo '<div class="row">';
          echo '<div class="col-md-2">';
          echo $this->Form->control('is_active', ['checked' => true]);
          echo '</div>';
          echo '<div class="col-md-4">';          
          echo $this->element('msg', ['msg' => __('Note-is-active'), 'class' => 'info']);
          echo '</div>';
          echo '<div class="col-md-2">';
          echo $this->Form->control('sort');
          echo '</div>';
          echo '<div class="col-md-4">';          
          echo $this->element('msg', ['msg' => __('Note-sort'), 'class' => 'info']);
          echo '</div>';
          echo '</div>';
        
          echo '</div>'; // <!-- /.box-body -->

          echo $this->Form->control('document_reference_id', ['type' => 'hidden', 'value' => $document_reference_id]);
          echo $this->Form->control('document_reference_model_id', ['type' => 'hidden', 'value' => $document_reference_model_id]);
          echo $this->Form->control('document_owner_id', ['type' => 'hidden', 'value' => $document_owner_id]);
          echo $this->Form->control('document_owner_model_id', ['type' => 'hidden', 'value' => $document_owner_model_id]);

          echo '</fieldset>';

          echo $this->Form->button(__('Add'), ['class' => 'btn btn-primary pull-right']); 

          echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>