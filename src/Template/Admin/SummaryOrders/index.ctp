<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Summary Orders

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
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_pagato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('saldato_a') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modalita') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kSummaryOrders as $kSummaryOrder): ?>
                <tr>
                  <td><?= $this->Number->format($kSummaryOrder->id) ?></td>
                  <td><?= $kSummaryOrder->has('organization') ? $this->Html->link($kSummaryOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrder->organization->id]) : '' ?></td>
                  <td><?= $kSummaryOrder->has('user') ? $this->Html->link($kSummaryOrder->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrder->user->id]) : '' ?></td>
                  <td><?= $kSummaryOrder->has('delivery') ? $this->Html->link($kSummaryOrder->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kSummaryOrder->delivery->id]) : '' ?></td>
                  <td><?= $kSummaryOrder->has('order') ? $this->Html->link($kSummaryOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrder->order->id]) : '' ?></td>
                  <td><?= $this->Number->format($kSummaryOrder->importo) ?></td>
                  <td><?= $this->Number->format($kSummaryOrder->importo_pagato) ?></td>
                  <td><?= h($kSummaryOrder->saldato_a) ?></td>
                  <td><?= h($kSummaryOrder->modalita) ?></td>
                  <td><?= h($kSummaryOrder->created) ?></td>
                  <td><?= h($kSummaryOrder->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kSummaryOrder->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kSummaryOrder->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kSummaryOrder->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kSummaryOrder->id), 'class'=>'btn btn-danger btn-xs']) ?>
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