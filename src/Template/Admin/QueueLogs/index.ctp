<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Queue Logs

    <div class="pull-right"><?php echo $this->Form->postLink(__('Truncate'), ['action' => 'truncate'], ['confirm' => __('Are you sure you want to truncate table?'), 'class'=>'btn btn-danger btn-xs']) ?></div>
  </h1>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <?php
        echo $this->element('queue_logs_search', ['totResults' => $queueLogs->count(), 'queues' => $queues]);
        ?>
    </div>
  </div>

  <?php
  if(empty($queueLogs) || $queueLogs->count()==0) {
      echo '<div class="row">';
      echo '<div class="col-xs-12">';
      echo '<div class="box">';
      echo $this->element('msgResults', ['action_add' => false]);
      echo '</div>';
      echo '</div>';
      echo '</div>';
  }    
  else {
  ?>
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
                  <th scope="col">N°</th>
                  <th scope="col"><?= $this->Paginator->sort('queue_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('uuid') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('message') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('log') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('level') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($queueLogs as $numResult => $queueLog): ?>
                <tr>
                  <td><?php echo ($numResult+1);?></td>
                  <td><?= $queueLog->has('queue') ? $this->Html->link($queueLog->queue->name, ['controller' => 'Queues', 'action' => 'view', $queueLog->queue->id]) : '' ?></td>
                  <td><?= $queueLog->queue->has('master_scope') ? $this->Html->link($queueLog->queue->master_scope->name, ['controller' => 'Scopes', 'action' => 'view', $queueLog->queue->master_scope->id]) : '' ?></td>
                  <td><?= $queueLog->queue->has('slave_scope') ? $this->Html->link($queueLog->queue->slave_scope->name, ['controller' => 'Scopes', 'action' => 'view', $queueLog->queue->slave_scope->id]) : '' ?></td>                  
                  <td><?= h($queueLog->uuid) ?></td>
                  <td><?= $this->Text->autoParagraph($queueLog->message) ?></td>
                  <td><?= $this->Text->autoParagraph($queueLog->log) ?></td>
                  <td><?= h($queueLog->level) ?></td>
                  <td><?= h($queueLog->created) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>

    </div>
  </div>
  <?php
  }
  ?>

</section>
