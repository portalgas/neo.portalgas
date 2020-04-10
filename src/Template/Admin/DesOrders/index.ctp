<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Des Orders

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
                  <th scope="col"><?= $this->Paginator->sort('des_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('des_supplier_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('luogo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('nota_evidenza') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine_max') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasTrasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('trasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasCostMore') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_more') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasCostLess') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_less') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kDesOrders as $kDesOrder): ?>
                <tr>
                  <td><?= $this->Number->format($kDesOrder->id) ?></td>
                  <td><?= $this->Number->format($kDesOrder->des_id) ?></td>
                  <td><?= $this->Number->format($kDesOrder->des_supplier_id) ?></td>
                  <td><?= h($kDesOrder->luogo) ?></td>
                  <td><?= h($kDesOrder->nota_evidenza) ?></td>
                  <td><?= h($kDesOrder->data_fine_max) ?></td>
                  <td><?= h($kDesOrder->hasTrasport) ?></td>
                  <td><?= $this->Number->format($kDesOrder->trasport) ?></td>
                  <td><?= h($kDesOrder->hasCostMore) ?></td>
                  <td><?= $this->Number->format($kDesOrder->cost_more) ?></td>
                  <td><?= h($kDesOrder->hasCostLess) ?></td>
                  <td><?= $this->Number->format($kDesOrder->cost_less) ?></td>
                  <td><?= h($kDesOrder->state_code) ?></td>
                  <td><?= h($kDesOrder->created) ?></td>
                  <td><?= h($kDesOrder->modified) ?></td>
                  <td><?= $kDesOrder->has('organization') ? $this->Html->link($kDesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kDesOrder->organization->id]) : '' ?></td>
                  <td><?= $kDesOrder->has('order') ? $this->Html->link($kDesOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $kDesOrder->order->id]) : '' ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kDesOrder->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kDesOrder->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kDesOrder->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kDesOrder->id), 'class'=>'btn btn-danger btn-xs']) ?>
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