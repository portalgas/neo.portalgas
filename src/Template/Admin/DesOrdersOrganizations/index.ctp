<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Des Orders Organizations

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
                  <th scope="col"><?= $this->Paginator->sort('des_order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('luogo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('orario') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contatto_nominativo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contatto_telefono') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contatto_mail') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kDesOrdersOrganizations as $kDesOrdersOrganization): ?>
                <tr>
                  <td><?= $this->Number->format($kDesOrdersOrganization->id) ?></td>
                  <td><?= $this->Number->format($kDesOrdersOrganization->des_id) ?></td>
                  <td><?= $kDesOrdersOrganization->has('des_order') ? $this->Html->link($kDesOrdersOrganization->des_order->id, ['controller' => 'DesOrders', 'action' => 'view', $kDesOrdersOrganization->des_order->id]) : '' ?></td>
                  <td><?= $kDesOrdersOrganization->has('organization') ? $this->Html->link($kDesOrdersOrganization->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kDesOrdersOrganization->organization->id]) : '' ?></td>
                  <td><?= $kDesOrdersOrganization->has('order') ? $this->Html->link($kDesOrdersOrganization->order->id, ['controller' => 'Orders', 'action' => 'view', $kDesOrdersOrganization->order->organization_id]) : '' ?></td>
                  <td><?= h($kDesOrdersOrganization->luogo) ?></td>
                  <td><?= h($kDesOrdersOrganization->data) ?></td>
                  <td><?= h($kDesOrdersOrganization->orario) ?></td>
                  <td><?= h($kDesOrdersOrganization->contatto_nominativo) ?></td>
                  <td><?= h($kDesOrdersOrganization->contatto_telefono) ?></td>
                  <td><?= h($kDesOrdersOrganization->contatto_mail) ?></td>
                  <td><?= h($kDesOrdersOrganization->created) ?></td>
                  <td><?= h($kDesOrdersOrganization->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kDesOrdersOrganization->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kDesOrdersOrganization->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kDesOrdersOrganization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kDesOrdersOrganization->id), 'class'=>'btn btn-danger btn-xs']) ?>
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