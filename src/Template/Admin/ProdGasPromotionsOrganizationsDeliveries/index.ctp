<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Prod Gas Promotions Organizations Deliveries

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs']) ?></div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('supplier_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prod_gas_promotion_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isConfirmed') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kProdGasPromotionsOrganizationsDeliveries as $kProdGasPromotionsOrganizationsDelivery): ?>
                <tr>
                  <td><?= $this->Number->format($kProdGasPromotionsOrganizationsDelivery->id) ?></td>
                  <td><?= $kProdGasPromotionsOrganizationsDelivery->has('supplier') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->supplier->id]) : '' ?></td>
                  <td><?= $kProdGasPromotionsOrganizationsDelivery->has('prod_gas_promotion') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->prod_gas_promotion->id]) : '' ?></td>
                  <td><?= $kProdGasPromotionsOrganizationsDelivery->has('organization') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->organization->id]) : '' ?></td>
                  <td><?= $kProdGasPromotionsOrganizationsDelivery->has('delivery') ? $this->Html->link($kProdGasPromotionsOrganizationsDelivery->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kProdGasPromotionsOrganizationsDelivery->delivery->organization_id]) : '' ?></td>
                  <td><?= h($kProdGasPromotionsOrganizationsDelivery->isConfirmed) ?></td>
                  <td><?= h($kProdGasPromotionsOrganizationsDelivery->created) ?></td>
                  <td><?= h($kProdGasPromotionsOrganizationsDelivery->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kProdGasPromotionsOrganizationsDelivery->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kProdGasPromotionsOrganizationsDelivery->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kProdGasPromotionsOrganizationsDelivery->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kProdGasPromotionsOrganizationsDelivery->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>