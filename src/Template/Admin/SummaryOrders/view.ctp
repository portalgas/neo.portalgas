<section class="content-header">
  <h1>
    K Summary Order
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
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $kSummaryOrder->has('organization') ? $this->Html->link($kSummaryOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kSummaryOrder->has('user') ? $this->Html->link($kSummaryOrder->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrder->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $kSummaryOrder->has('delivery') ? $this->Html->link($kSummaryOrder->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kSummaryOrder->delivery->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kSummaryOrder->has('order') ? $this->Html->link($kSummaryOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrder->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Saldato A') ?></dt>
            <dd><?= h($kSummaryOrder->saldato_a) ?></dd>
            <dt scope="row"><?= __('Modalita') ?></dt>
            <dd><?= h($kSummaryOrder->modalita) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrder->id) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrder->importo) ?></dd>
            <dt scope="row"><?= __('Importo Pagato') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrder->importo_pagato) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kSummaryOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kSummaryOrder->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
