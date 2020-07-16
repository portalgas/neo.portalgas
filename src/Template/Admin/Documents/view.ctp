<section class="content-header">
  <h1>
    <?php echo __('Document');?>
    <small><?php echo __('View'); ?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-info"></i>
          <h3 class="box-title"><?php echo __('Information'); ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <dl class="dl-horizontal">
            <dt scope="row"><?= __('Document State') ?></dt>
            <dd><?= $document->has('document_state') ? $this->Html->link($document->document_state->name, ['controller' => 'DocumentStates', 'action' => 'view', $document->document_state->id]) : '' ?></dd>
            <dt scope="row"><?= __('Document Type') ?></dt>
            <dd><?= $document->has('document_type') ? $this->Html->link($document->document_type->name, ['controller' => 'DocumentTypes', 'action' => 'view', $document->document_type->id]) : '' ?></dd>
            <dt scope="row"><?= __('Document Reference Model') ?></dt>
            <dd><?= $document->has('document_reference_model') ? $this->Html->link($document->document_reference_model->name, ['controller' => 'DocumentReferenceModels', 'action' => 'view', $document->document_reference_model->id]) : '' ?></dd>
            <dt scope="row"><?= __('Document Owner Model') ?></dt>
            <dd><?= $document->has('document_owner_model') ? $this->Html->link($document->document_owner_model->name, ['controller' => 'DocumentOwnerModels', 'action' => 'view', $document->document_owner_model->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($document->name) ?></dd>
            <dt scope="row"><?= __('Path') ?></dt>
            <dd><?= h($document->path) ?></dd>
            <dt scope="row"><?= __('File Preview Path') ?></dt>
            <dd><?= h($document->file_preview_path) ?></dd>
            <dt scope="row"><?= __('File Name') ?></dt>
            <dd><?= h($document->file_name) ?></dd>
            <dt scope="row"><?= __('File Ext') ?></dt>
            <dd><?= h($document->file_ext) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($document->id) ?></dd>
            <dt scope="row"><?= __('Document Reference Id') ?></dt>
            <dd><?= $this->Number->format($document->document_reference_id) ?></dd>
            <dt scope="row"><?= __('Document Owner Id') ?></dt>
            <dd><?= $this->Number->format($document->document_owner_id) ?></dd>
            <dt scope="row"><?= __('File Size') ?></dt>
            <dd><?= $this->Number->format($document->file_size) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($document->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($document->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($document->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $document->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $document->is_active ? __('Yes') : __('No'); ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Descri') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($document->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
