<section class="content-header">
  <h1>
    Gas Group Order
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
            <dd><?= $gasGroupOrder->has('organization') ? $this->Html->link($gasGroupOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $gasGroupOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Gas Group') ?></dt>
            <dd><?= $gasGroupOrder->has('gas_group') ? $this->Html->link($gasGroupOrder->gas_group->name, ['controller' => 'GasGroups', 'action' => 'view', $gasGroupOrder->gas_group->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $gasGroupOrder->has('order') ? $this->Html->link($gasGroupOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $gasGroupOrder->order->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($gasGroupOrder->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($gasGroupOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($gasGroupOrder->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
