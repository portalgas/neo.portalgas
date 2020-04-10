<section class="content-header">
  <h1>
    K Summary Order Aggregate
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
            <dd><?= $kSummaryOrderAggregate->has('organization') ? $this->Html->link($kSummaryOrderAggregate->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrderAggregate->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kSummaryOrderAggregate->has('user') ? $this->Html->link($kSummaryOrderAggregate->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrderAggregate->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kSummaryOrderAggregate->has('order') ? $this->Html->link($kSummaryOrderAggregate->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrderAggregate->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderAggregate->id) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kSummaryOrderAggregate->importo) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kSummaryOrderAggregate->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kSummaryOrderAggregate->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
