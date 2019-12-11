<section class="content-header">
  <h1>
    Queue
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
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($queue->id) ?></dd>
            <dt scope="row"><?= __('Code') ?></dt>
            <dd><?= h($queue->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($queue->name) ?></dd>
            <dt scope="row"><?= __('Component') ?></dt>
            <dd><?= h($queue->component) ?></dd>
            <dt scope="row"><?= __('Master Scope') ?></dt>
            <dd><?= $queue->has('master_scope') ? $this->Html->link($queue->master_scope->name, ['controller' => 'Scopes', 'action' => 'view', $queue->master_scope->id]) : '' ?></dd>
            <dt scope="row"><?= __('Slave Scope') ?></dt>
            <dd><?= $queue->has('slave_scope') ? $this->Html->link($queue->slave_scope->name, ['controller' => 'Scopes', 'action' => 'view', $queue->slave_scope->id]) : '' ?></dd>
            <dt scope="row"><?= __('Descri') ?></dt>
            <dd><?= $this->Text->autoParagraph($queue->descri) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($queue->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($queue->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $queue->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $queue->is_active ? __('Yes') : __('No'); ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
