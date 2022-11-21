<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Gas Group Deliveries

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
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('luogo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('orario_da') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('orario_a') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('nota_evidenza') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato_elaborazione') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('gcalendar_event_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($gasGroupDeliveries as $gasGroupDelivery): ?>
                <tr>
                  <td><?= $this->Number->format($gasGroupDelivery->id) ?></td>
                  <td><?= $gasGroupDelivery->has('organization') ? $this->Html->link($gasGroupDelivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $gasGroupDelivery->organization->id]) : '' ?></td>
                  <td><?= $gasGroupDelivery->has('delivery') ? $this->Html->link($gasGroupDelivery->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $gasGroupDelivery->delivery->organization_id]) : '' ?></td>
                  <td><?= h($gasGroupDelivery->luogo) ?></td>
                  <td><?= h($gasGroupDelivery->data) ?></td>
                  <td><?= h($gasGroupDelivery->orario_da) ?></td>
                  <td><?= h($gasGroupDelivery->orario_a) ?></td>
                  <td><?= h($gasGroupDelivery->nota_evidenza) ?></td>
                  <td><?= h($gasGroupDelivery->stato_elaborazione) ?></td>
                  <td><?= h($gasGroupDelivery->gcalendar_event_id) ?></td>
                  <td><?= h($gasGroupDelivery->created) ?></td>
                  <td><?= h($gasGroupDelivery->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $gasGroupDelivery->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $gasGroupDelivery->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $gasGroupDelivery->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gasGroupDelivery->id), 'class'=>'btn btn-danger btn-xs']) ?>
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