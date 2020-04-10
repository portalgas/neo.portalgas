<section class="content-header">
  <h1>
    K Des Order
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
            <dt scope="row"><?= __('Luogo') ?></dt>
            <dd><?= h($kDesOrder->luogo) ?></dd>
            <dt scope="row"><?= __('Nota Evidenza') ?></dt>
            <dd><?= h($kDesOrder->nota_evidenza) ?></dd>
            <dt scope="row"><?= __('HasTrasport') ?></dt>
            <dd><?= h($kDesOrder->hasTrasport) ?></dd>
            <dt scope="row"><?= __('HasCostMore') ?></dt>
            <dd><?= h($kDesOrder->hasCostMore) ?></dd>
            <dt scope="row"><?= __('HasCostLess') ?></dt>
            <dd><?= h($kDesOrder->hasCostLess) ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($kDesOrder->state_code) ?></dd>
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $kDesOrder->has('organization') ? $this->Html->link($kDesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kDesOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kDesOrder->has('order') ? $this->Html->link($kDesOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $kDesOrder->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->id) ?></dd>
            <dt scope="row"><?= __('Des Id') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->des_id) ?></dd>
            <dt scope="row"><?= __('Des Supplier Id') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->des_supplier_id) ?></dd>
            <dt scope="row"><?= __('Trasport') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->trasport) ?></dd>
            <dt scope="row"><?= __('Cost More') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->cost_more) ?></dd>
            <dt scope="row"><?= __('Cost Less') ?></dt>
            <dd><?= $this->Number->format($kDesOrder->cost_less) ?></dd>
            <dt scope="row"><?= __('Data Fine Max') ?></dt>
            <dd><?= h($kDesOrder->data_fine_max) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kDesOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kDesOrder->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Nota') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kDesOrder->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
