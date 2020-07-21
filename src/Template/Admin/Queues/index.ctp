<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Queues

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs']) ?></div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('queue_mapping_type_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('component') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_db_datasource') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_db_datasource') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('log_type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($queues as $queue): ?>
                <tr>
                  <td><?= $this->Number->format($queue->id) ?></td>
                  <td><?= $queue->has('queue_mapping_type') ? $this->Html->link($queue->queue_mapping_type->name, ['controller' => 'QueueMappingTypes', 'action' => 'view', $queue->queue_mapping_type->id]) : '' ?></td>                  
                  <td><?= h($queue->code) ?></td>
                  <td><?= h($queue->name) ?></td>
                  <td><?= h($queue->component) ?></td>
                  <td><?= $queue->has('master_scope') ? $this->Html->link($queue->master_scope->name, ['controller' => 'MasterScopes', 'action' => 'view', $queue->master_scope->id]) : '' ?></td>
                  <td><?php
                  if(empty($queue->master_db_datasource))
                      echo $queue->master_scope->db_datasource;
                  else
                      $queue->master_db_datasource;
                   ?></td>                  
                  <td><?= $queue->has('slave_scope') ? $this->Html->link($queue->slave_scope->name, ['controller' => 'SlaveScopes', 'action' => 'view', $queue->slave_scope->id]) : '' ?></td>
                  <td><?php
                  if(empty($queue->slave_db_datasource))
                      echo $queue->slave_scope->db_datasource;
                  else
                      $queue->slave_db_datasource;
                   ?></td>
                  <td><?= h($queue->log_type) ?></td>                
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($queue, $queue->is_system) ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($queue, $queue->is_active) ?></td>

                  <td><?= h($queue->created) ?></td>
                  <td><?= h($queue->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $queue->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $queue->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?php
                      if(!$queue->is_system)
                        $this->Form->postLink(__('Delete'), ['action' => 'delete', $queue->id], ['confirm' => __('Are you sure you want to delete # {0}?', $queue->id), 'class'=>'btn btn-danger btn-xs']);
                      ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>