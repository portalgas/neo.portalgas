<section class="content-header">
  <h1>
    K Prod Gas Promotions Organization
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
            <dt scope="row"><?= __('Prod Gas Promotion') ?></dt>
            <dd><?= $kProdGasPromotionsOrganization->has('prod_gas_promotion') ? $this->Html->link($kProdGasPromotionsOrganization->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kProdGasPromotionsOrganization->prod_gas_promotion->id]) : '' ?></dd>
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $kProdGasPromotionsOrganization->has('organization') ? $this->Html->link($kProdGasPromotionsOrganization->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotionsOrganization->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kProdGasPromotionsOrganization->has('order') ? $this->Html->link($kProdGasPromotionsOrganization->order->id, ['controller' => 'Orders', 'action' => 'view', $kProdGasPromotionsOrganization->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('HasTrasport') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganization->hasTrasport) ?></dd>
            <dt scope="row"><?= __('HasCostMore') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganization->hasCostMore) ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kProdGasPromotionsOrganization->has('user') ? $this->Html->link($kProdGasPromotionsOrganization->user->name, ['controller' => 'Users', 'action' => 'view', $kProdGasPromotionsOrganization->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganization->state_code) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotionsOrganization->id) ?></dd>
            <dt scope="row"><?= __('Trasport') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotionsOrganization->trasport) ?></dd>
            <dt scope="row"><?= __('Cost More') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotionsOrganization->cost_more) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganization->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganization->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Nota Supplier') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kProdGasPromotionsOrganization->nota_supplier); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Nota User') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kProdGasPromotionsOrganization->nota_user); ?>
        </div>
      </div>
    </div>
  </div>
</section>
