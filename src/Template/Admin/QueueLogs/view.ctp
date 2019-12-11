<section class="content-header">
  <h1>
    Queue Log
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
            <dd><?= $queueLog->has('queue') ? $this->Html->link($queueLog->queue->name, ['controller' => 'Queues', 'action' => 'view', $queueLog->queue->id]) : '' ?></dd>
            <dt scope="row"><?= __('Uuid') ?></dt>
            <dd><?= h($queueLog->uuid) ?></dd>
            <dt scope="row"><?= __('Level') ?></dt>
            <dd><?= h($queueLog->level) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($queueLog->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($queueLog->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($queueLog->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Message') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($queueLog->message); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Log') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($queueLog->log); ?>
        </div>
      </div>
    </div>
  </div>
</section>
