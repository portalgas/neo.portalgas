<section class="content-header">
  <h1>
    K Summary Order Trasport
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
            <dd><?= $kSummaryOrderTrasport->has('organization') ? $this->Html->link($kSummaryOrderTrasport->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrderTrasport->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kSummaryOrderTrasport->has('user') ? $this->Html->link($kSummaryOrderTrasport->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrderTrasport->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kSummaryOrderTrasport->has('order') ? $this->Html->link($kSummaryOrderTrasport->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrderTrasport->order->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderTrasport->id) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderTrasport->importo) ?></dd>
            <dt scope="row"><?= __('Peso') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderTrasport->peso) ?></dd>
            <dt scope="row"><?= __('Importo Trasport') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderTrasport->importo_trasport) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kSummaryOrderTrasport->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kSummaryOrderTrasport->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
