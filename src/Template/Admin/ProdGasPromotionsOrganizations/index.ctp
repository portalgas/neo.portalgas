<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Prod Gas Promotions Organizations

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
                  <th scope="col"><?= $this->Paginator->sort('prod_gas_promotion_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasTrasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('trasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasCostMore') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_more') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kProdGasPromotionsOrganizations as $kProdGasPromotionsOrganization): ?>
                <tr>
                  <td><?= $this->Number->format($kProdGasPromotionsOrganization->id) ?></td>
                  <td><?= $kProdGasPromotionsOrganization->has('prod_gas_promotion') ? $this->Html->link($kProdGasPromotionsOrganization->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kProdGasPromotionsOrganization->prod_gas_promotion->id]) : '' ?></td>
                  <td><?= $kProdGasPromotionsOrganization->has('organization') ? $this->Html->link($kProdGasPromotionsOrganization->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotionsOrganization->organization->id]) : '' ?></td>
                  <td><?= $kProdGasPromotionsOrganization->has('order') ? $this->Html->link($kProdGasPromotionsOrganization->order->id, ['controller' => 'Orders', 'action' => 'view', $kProdGasPromotionsOrganization->order->id]) : '' ?></td>
                  <td><?= h($kProdGasPromotionsOrganization->hasTrasport) ?></td>
                  <td><?= $this->Number->format($kProdGasPromotionsOrganization->trasport) ?></td>
                  <td><?= h($kProdGasPromotionsOrganization->hasCostMore) ?></td>
                  <td><?= $this->Number->format($kProdGasPromotionsOrganization->cost_more) ?></td>
                  <td><?= $kProdGasPromotionsOrganization->has('user') ? $this->Html->link($kProdGasPromotionsOrganization->user->name, ['controller' => 'Users', 'action' => 'view', $kProdGasPromotionsOrganization->user->id]) : '' ?></td>
                  <td><?= h($kProdGasPromotionsOrganization->state_code) ?></td>
                  <td><?= h($kProdGasPromotionsOrganization->created) ?></td>
                  <td><?= h($kProdGasPromotionsOrganization->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kProdGasPromotionsOrganization->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kProdGasPromotionsOrganization->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kProdGasPromotionsOrganization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kProdGasPromotionsOrganization->id), 'class'=>'btn btn-danger btn-xs']) ?>
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