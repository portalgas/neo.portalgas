<section class="content-header">
  <h1>
    Queue Mapping Type
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
            <dd><?= h($queueMappingType->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($queueMappingType->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($queueMappingType->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($queueMappingType->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($queueMappingType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($queueMappingType->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $queueMappingType->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $queueMappingType->is_active ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($queueMappingType->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('ParamConfigs') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($queueMappingType->paramConfigs); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Queues') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($queueMappingType->queues)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Queue Mapping Type Id') ?></th>
                    <th scope="col"><?= __('Code') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Component') ?></th>
                    <th scope="col"><?= __('Master Scope Id') ?></th>
                    <th scope="col"><?= __('Slave Scope Id') ?></th>
                    <th scope="col"><?= __('Descri') ?></th>
                    <th scope="col"><?= __('Is System') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($queueMappingType->queues as $queues): ?>
              <tr>
                    <td><?= h($queues->id) ?></td>
                    <td><?= h($queues->queue_mapping_type_id) ?></td>
                    <td><?= h($queues->code) ?></td>
                    <td><?= h($queues->name) ?></td>
                    <td><?= h($queues->component) ?></td>
                    <td><?= h($queues->master_scope_id) ?></td>
                    <td><?= h($queues->slave_scope_id) ?></td>
                    <td><?= h($queues->descri) ?></td>
                    <td><?= h($queues->is_system) ?></td>
                    <td><?= h($queues->is_active) ?></td>
                    <td><?= h($queues->created) ?></td>
                    <td><?= h($queues->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Queues', 'action' => 'view', $queues->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Queues', 'action' => 'edit', $queues->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Queues', 'action' => 'delete', $queues->id], ['confirm' => __('Are you sure you want to delete # {0}?', $queues->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
