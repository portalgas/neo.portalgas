<section class="content-header">
  <h1>
    Queue Table
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
            <dd><?= $queueTable->has('queue') ? $this->Html->link($queueTable->queue->id, ['controller' => 'Queues', 'action' => 'view', $queueTable->queue->id]) : '' ?></dd>
            <dt scope="row"><?= __('Table') ?></dt>
            <dd><?= $queueTable->has('table') ? $this->Html->link($queueTable->table->name, ['controller' => 'Tables', 'action' => 'view', $queueTable->table->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($queueTable->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($queueTable->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($queueTable->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($queueTable->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
