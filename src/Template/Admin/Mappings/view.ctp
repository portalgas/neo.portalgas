<section class="content-header">
  <h1>
    Mapping
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
            <dt scope="row"><?= __('Queue') ?></dt>
            <dd><?= $mapping->has('queue') ? $this->Html->link($mapping->queue->name, ['controller' => 'Queues', 'action' => 'view', $mapping->queue->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($mapping->name) ?></dd>
            <dt scope="row"><?= __('Master Scope') ?></dt>
            <dd><?= $mapping->has('master_scope') ? $this->Html->link($mapping->master_scope->name, ['controller' => 'MasterScopes', 'action' => 'view', $mapping->master_scope->id]) : '' ?></dd>
            <dt scope="row"><?= __('Master Table') ?></dt>
            <dd><?= $mapping->has('master_table') ? $this->Html->link($mapping->master_table->name, ['controller' => 'MasterTables', 'action' => 'view', $mapping->master_table->id]) : '' ?></dd>
            <dt scope="row"><?= __('Master Column') ?></dt>
            <dd><?= h($mapping->master_column) ?></dd>
            <dt scope="row"><?= __('Master Xml Xpath') ?></dt>
            <dd><?= h($mapping->master_xml_xpath) ?></dd>
            <dt scope="row"><?= __('Slave Scope') ?></dt>
            <dd><?= $mapping->has('slave_scope') ? $this->Html->link($mapping->slave_scope->name, ['controller' => 'SlaveScopes', 'action' => 'view', $mapping->slave_scope->id]) : '' ?></dd>
            <dt scope="row"><?= __('Slave Table') ?></dt>
            <dd><?= $mapping->has('slave_table') ? $this->Html->link($mapping->slave_table->name, ['controller' => 'SlaveTables', 'action' => 'view', $mapping->slave_table->id]) : '' ?></dd>
            <dt scope="row"><?= __('Slave Column') ?></dt>
            <dd><?= h($mapping->slave_column) ?></dd>
            <dt scope="row"><?= __('Mapping Type') ?></dt>
            <dd><?= $mapping->has('mapping_type') ? $this->Html->link($mapping->mapping_type->name, ['controller' => 'MappingTypes', 'action' => 'view', $mapping->mapping_type->id]) : '' ?></dd>
            <dt scope="row"><?= __('Queue Table') ?></dt>
            <dd><?= $mapping->has('queue_table') ? $this->Html->link($mapping->queue_table->id, ['controller' => 'QueueTables', 'action' => 'view', $mapping->queue_table->id]) : '' ?></dd>
            <dt scope="row"><?= __('Value') ?></dt>
            <dd><?= h($mapping->value) ?></dd>
            <dt scope="row"><?= __('Value Default') ?></dt>
            <dd><?= h($mapping->value_default) ?></dd>
            <dt scope="row"><?= __('Parameters') ?></dt>
            <dd><?= h($mapping->parameters) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($mapping->id) ?></dd>
            <dt scope="row"><?= __('Master Csv Num Col') ?></dt>
            <dd><?= $this->Number->format($mapping->master_csv_num_col) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($mapping->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($mapping->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($mapping->modified) ?></dd>
            <dt scope="row"><?= __('Is Required') ?></dt>
            <dd><?= $mapping->is_required ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $mapping->is_active ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($mapping->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
