<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Stat Orders

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
                  <th scope="col"><?= $this->Paginator->sort('supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('supplier_organization_name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('supplier_img1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stat_delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stat_delivery_year') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_inizio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_fattura_importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_doc1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_data_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_importo_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('request_payment_num') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kStatOrders as $kStatOrder): ?>
                <tr>
                  <td><?= $this->Number->format($kStatOrder->id) ?></td>
                  <td><?= $kStatOrder->has('organization') ? $this->Html->link($kStatOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatOrder->organization->id]) : '' ?></td>
                  <td><?= $this->Number->format($kStatOrder->supplier_organization_id) ?></td>
                  <td><?= h($kStatOrder->supplier_organization_name) ?></td>
                  <td><?= h($kStatOrder->supplier_img1) ?></td>
                  <td><?= $this->Number->format($kStatOrder->stat_delivery_id) ?></td>
                  <td><?= $this->Number->format($kStatOrder->stat_delivery_year) ?></td>
                  <td><?= h($kStatOrder->data_inizio) ?></td>
                  <td><?= h($kStatOrder->data_fine) ?></td>
                  <td><?= $this->Number->format($kStatOrder->importo) ?></td>
                  <td><?= $this->Number->format($kStatOrder->tesoriere_fattura_importo) ?></td>
                  <td><?= h($kStatOrder->tesoriere_doc1) ?></td>
                  <td><?= h($kStatOrder->tesoriere_data_pay) ?></td>
                  <td><?= $this->Number->format($kStatOrder->tesoriere_importo_pay) ?></td>
                  <td><?= h($kStatOrder->request_payment_num) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kStatOrder->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kStatOrder->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kStatOrder->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kStatOrder->id), 'class'=>'btn btn-danger btn-xs']) ?>
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