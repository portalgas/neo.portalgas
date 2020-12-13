<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Summary Order Trasports

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
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('peso') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_trasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kSummaryOrderTrasports as $kSummaryOrderTrasport): ?>
                <tr>
                  <td><?= $this->Number->format($kSummaryOrderTrasport->id) ?></td>
                  <td><?= $kSummaryOrderTrasport->has('organization') ? $this->Html->link($kSummaryOrderTrasport->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSummaryOrderTrasport->organization->id]) : '' ?></td>
                  <td><?= $kSummaryOrderTrasport->has('user') ? $this->Html->link($kSummaryOrderTrasport->user->name, ['controller' => 'Users', 'action' => 'view', $kSummaryOrderTrasport->user->id]) : '' ?></td>
                  <td><?= $kSummaryOrderTrasport->has('order') ? $this->Html->link($kSummaryOrderTrasport->order->id, ['controller' => 'Orders', 'action' => 'view', $kSummaryOrderTrasport->order->organization_id]) : '' ?></td>
                  <td><?= $this->Number->format($kSummaryOrderTrasport->importo) ?></td>
                  <td><?= $this->Number->format($kSummaryOrderTrasport->peso) ?></td>
                  <td><?= $this->Number->format($kSummaryOrderTrasport->importo_trasport) ?></td>
                  <td><?= h($kSummaryOrderTrasport->created) ?></td>
                  <td><?= h($kSummaryOrderTrasport->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kSummaryOrderTrasport->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kSummaryOrderTrasport->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kSummaryOrderTrasport->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kSummaryOrderTrasport->id), 'class'=>'btn btn-danger btn-xs']) ?>
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