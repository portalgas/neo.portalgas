<section class="content-header">
  <h1>
    K Summary Order Cost Less
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
            <dd><?= $kSummaryOrderCostLess->has('organization') ? $this->Html->link($kSummaryOrderCostLess->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrderCostLess->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kSummaryOrderCostLess->has('user') ? $this->Html->link($kSummaryOrderCostLess->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrderCostLess->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kSummaryOrderCostLess->has('order') ? $this->Html->link($kSummaryOrderCostLess->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrderCostLess->order->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderCostLess->id) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderCostLess->importo) ?></dd>
            <dt scope="row"><?= __('Peso') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderCostLess->peso) ?></dd>
            <dt scope="row"><?= __('Importo Cost Less') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderCostLess->importo_cost_less) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kSummaryOrderCostLess->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kSummaryOrderCostLess->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
