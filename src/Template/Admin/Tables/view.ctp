<section class="content-header">
  <h1>
    Table
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
            <dt scope="row"><?= __('Scope') ?></dt>
            <dd><?= $table->has('scope') ? $this->Html->link($table->scope->name, ['controller' => 'Scopes', 'action' => 'view', $table->scope->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($table->name) ?></dd>
            <dt scope="row"><?= __('Table Name') ?></dt>
            <dd><?= h($table->table_name) ?></dd>
            <dt scope="row"><?= __('Entity') ?></dt>
            <dd><?= h($table->entity) ?></dd>
            <dt scope="row"><?= __('Where Key') ?></dt>
            <dd><?= h($table->where_key) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($table->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($table->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($table->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $table->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $table->is_active ? __('Yes') : __('No'); ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Queue Tables') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($table->queue_tables)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Queue Id') ?></th>
                    <th scope="col"><?= __('Table Id') ?></th>
                    <th scope="col"><?= __('Sort') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($table->queue_tables as $queueTables): ?>
              <tr>
                    <td><?= h($queueTables->id) ?></td>
                    <td><?= h($queueTables->queue_id) ?></td>
                    <td><?= h($queueTables->table_id) ?></td>
                    <td><?= h($queueTables->sort) ?></td>
                    <td><?= h($queueTables->created) ?></td>
                    <td><?= h($queueTables->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'QueueTables', 'action' => 'view', $queueTables->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'QueueTables', 'action' => 'edit', $queueTables->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'QueueTables', 'action' => 'delete', $queueTables->id], ['confirm' => __('Are you sure you want to delete # {0}?', $queueTables->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
