<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Queue Tables

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
                  <th scope="col"><?= $this->Paginator->sort('queue_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('table_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('before_save') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('after_save') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($queueTables as $queueTable): ?>
                <tr>
                  <td><?= $this->Number->format($queueTable->id) ?></td>
                  <td><?= $queueTable->has('queue') ? $this->Html->link($queueTable->queue->name.' ('.$queueTable->queue->name.')', ['controller' => 'Queues', 'action' => 'view', $queueTable->queue->id]) : '' ?></td>
                  <td><?= $queueTable->has('table') ? $this->Html->link($queueTable->table->name, ['controller' => 'Tables', 'action' => 'view', $queueTable->table->id]) : '' ?></td>
                  <td><?= $queueTable->before_save ?></td>
                  <td><?= $queueTable->after_save ?></td>
                  <td><?= $this->Number->format($queueTable->sort) ?></td>
                  <td><?= h($queueTable->created) ?></td>
                  <td><?= h($queueTable->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $queueTable->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $queueTable->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $queueTable->id], ['confirm' => __('Are you sure you want to delete # {0}?', $queueTable->id), 'class'=>'btn btn-danger btn-xs']) ?>
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