<section class="content-header">
  <h1>
    K Prod Gas Promotions Organizations Delivery
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
            <dt scope="row"><?= __('Supplier') ?></dt>
            <dd><?= $kProdGasPromotionsOrganizationsDelivery->has('supplier') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->supplier->id]) : '' ?></dd>
            <dt scope="row"><?= __('Prod Gas Promotion') ?></dt>
            <dd><?= $kProdGasPromotionsOrganizationsDelivery->has('prod_gas_promotion') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->prod_gas_promotion->id]) : '' ?></dd>
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $kProdGasPromotionsOrganizationsDelivery->has('organization') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $kProdGasPromotionsOrganizationsDelivery->has('delivery') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->delivery->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('IsConfirmed') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganizationsDelivery->isConfirmed) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotionsOrganizationsDelivery->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganizationsDelivery->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kProdGasPromotionsOrganizationsDelivery->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
