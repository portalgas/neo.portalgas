<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Deliveries

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
                  <th scope="col"><?= $this->Paginator->sort('luogo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('orario_da') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('orario_a') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('nota_evidenza') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isToStoreroom') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isToStoreroomPay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato_elaborazione') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isVisibleFrontEnd') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isVisibleBackOffice') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sys') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('gcalendar_event_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($deliveries as $kDelivery): ?>
                <tr>
                  <td><?= $this->Number->format($kDelivery->id) ?></td>
                  <td><?= $kDelivery->has('organization') ? $this->Html->link($kDelivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kDelivery->organization->id]) : '' ?></td>
                  <td><?= h($kDelivery->luogo) ?></td>
                  <td><?= h($kDelivery->data) ?></td>
                  <td><?= h($kDelivery->orario_da) ?></td>
                  <td><?= h($kDelivery->orario_a) ?></td>
                  <td><?= h($kDelivery->nota_evidenza) ?></td>
                  <td><?= h($kDelivery->isToStoreroom) ?></td>
                  <td><?= h($kDelivery->isToStoreroomPay) ?></td>
                  <td><?= h($kDelivery->stato_elaborazione) ?></td>
                  <td><?= h($kDelivery->isVisibleFrontEnd) ?></td>
                  <td><?= h($kDelivery->isVisibleBackOffice) ?></td>
                  <td><?= h($kDelivery->sys) ?></td>
                  <td><?= h($kDelivery->gcalendar_event_id) ?></td>
                  <td><?= h($kDelivery->created) ?></td>
                  <td><?= h($kDelivery->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kDelivery->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kDelivery->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kDelivery->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kDelivery->id), 'class'=>'btn btn-danger btn-xs']) ?>
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