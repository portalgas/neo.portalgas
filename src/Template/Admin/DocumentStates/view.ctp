<section class="content-header">
  <h1>
    Document State
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
            <dt scope="row"><?= __('Code') ?></dt>
            <dd><?= h($documentState->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($documentState->name) ?></dd>
            <dt scope="row"><?= __('Css Color') ?></dt>
            <dd><?= h($documentState->css_color) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($documentState->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($documentState->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($documentState->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($documentState->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $documentState->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $documentState->is_active ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default Ini') ?></dt>
            <dd><?= $documentState->is_default_ini ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default End') ?></dt>
            <dd><?= $documentState->is_default_end ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($documentState->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Documents') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($documentState->documents)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Document State Id') ?></th>
                    <th scope="col"><?= __('Document Type Id') ?></th>
                    <th scope="col"><?= __('Document Reference Model Id') ?></th>
                    <th scope="col"><?= __('Document Reference Id') ?></th>
                    <th scope="col"><?= __('Document Owner Model Id') ?></th>
                    <th scope="col"><?= __('Document Owner Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Path') ?></th>
                    <th scope="col"><?= __('File Preview Path') ?></th>
                    <th scope="col"><?= __('File Name') ?></th>
                    <th scope="col"><?= __('File Size') ?></th>
                    <th scope="col"><?= __('File Ext') ?></th>
                    <th scope="col"><?= __('File Type') ?></th>
                    <th scope="col"><?= __('Descri') ?></th>
                    <th scope="col"><?= __('Is System') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Sort') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($documentState->documents as $documents): ?>
              <tr>
                    <td><?= h($documents->id) ?></td>
                    <td><?= h($documents->document_state_id) ?></td>
                    <td><?= h($documents->document_type_id) ?></td>
                    <td><?= h($documents->document_reference_model_id) ?></td>
                    <td><?= h($documents->document_reference_id) ?></td>
                    <td><?= h($documents->document_owner_model_id) ?></td>
                    <td><?= h($documents->document_owner_id) ?></td>
                    <td><?= h($documents->name) ?></td>
                    <td><?= h($documents->path) ?></td>
                    <td><?= h($documents->file_preview_path) ?></td>
                    <td><?= h($documents->file_name) ?></td>
                    <td><?= h($documents->file_size) ?></td>
                    <td><?= h($documents->file_ext) ?></td>
                    <td><?= h($documents->file_type) ?></td>
                    <td><?= h($documents->descri) ?></td>
                    <td><?= h($documents->is_system) ?></td>
                    <td><?= h($documents->is_active) ?></td>
                    <td><?= h($documents->sort) ?></td>
                    <td><?= h($documents->created) ?></td>
                    <td><?= h($documents->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Documents', 'action' => 'view', $documents->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Documents', 'action' => 'edit', $documents->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Documents', 'action' => 'delete', $documents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $documents->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
