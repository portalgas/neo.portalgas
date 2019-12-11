<section class="content-header">
  <h1>
    Mapping Type
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
            <dd><?= h($mappingType->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($mappingType->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($mappingType->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($mappingType->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($mappingType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($mappingType->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $mappingType->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $mappingType->is_active ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default Ini') ?></dt>
            <dd><?= $mappingType->is_default_ini ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default End') ?></dt>
            <dd><?= $mappingType->is_default_end ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($mappingType->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Mappings') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($mappingType->mappings)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Descri') ?></th>
                    <th scope="col"><?= __('Master Scope Id') ?></th>
                    <th scope="col"><?= __('Master Table Id') ?></th>
                    <th scope="col"><?= __('Master Column') ?></th>
                    <th scope="col"><?= __('Slave Scope Id') ?></th>
                    <th scope="col"><?= __('Slave Table Id') ?></th>
                    <th scope="col"><?= __('Slave Column') ?></th>
                    <th scope="col"><?= __('Mapping Type Id') ?></th>
                    <th scope="col"><?= __('Value') ?></th>
                    <th scope="col"><?= __('Parameters') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Sort') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($mappingType->mappings as $mappings): ?>
              <tr>
                    <td><?= h($mappings->id) ?></td>
                    <td><?= h($mappings->name) ?></td>
                    <td><?= h($mappings->descri) ?></td>
                    <td><?= h($mappings->master_scope_id) ?></td>
                    <td><?= h($mappings->master_table_id) ?></td>
                    <td><?= h($mappings->master_column) ?></td>
                    <td><?= h($mappings->slave_scope_id) ?></td>
                    <td><?= h($mappings->slave_table_id) ?></td>
                    <td><?= h($mappings->slave_column) ?></td>
                    <td><?= h($mappings->mapping_type_id) ?></td>
                    <td><?= h($mappings->value) ?></td>
                    <td><?= h($mappings->parameters) ?></td>
                    <td><?= h($mappings->is_active) ?></td>
                    <td><?= h($mappings->sort) ?></td>
                    <td><?= h($mappings->created) ?></td>
                    <td><?= h($mappings->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Mappings', 'action' => 'view', $mappings->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Mappings', 'action' => 'edit', $mappings->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Mappings', 'action' => 'delete', $mappings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mappings->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
