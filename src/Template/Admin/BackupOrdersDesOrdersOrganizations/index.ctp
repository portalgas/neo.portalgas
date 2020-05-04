<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Backup Orders Des Orders Organizations

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
              <?php foreach ($kBackupOrdersDesOrdersOrganizations as $kBackupOrdersDesOrdersOrganization): ?>
                <tr>
                  <td><?= $this->Number->format($kBackupOrdersDesOrdersOrganization->id) ?></td>
                  <td><?= $this->Number->format($kBackupOrdersDesOrdersOrganization->des_id) ?></td>
                  <td><?= $kBackupOrdersDesOrdersOrganization->has('des_order') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->des_order->id, ['controller' => 'DesOrders', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->des_order->id]) : '' ?></td>
                  <td><?= $kBackupOrdersDesOrdersOrganization->has('organization') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->organization->id]) : '' ?></td>
                  <td><?= $kBackupOrdersDesOrdersOrganization->has('order') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->order->id, ['controller' => 'Orders', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->order->id]) : '' ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->luogo) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->data) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->orario) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->contatto_nominativo) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->contatto_telefono) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->contatto_mail) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->created) ?></td>
                  <td><?= h($kBackupOrdersDesOrdersOrganization->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kBackupOrdersDesOrdersOrganization->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kBackupOrdersDesOrdersOrganization->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kBackupOrdersDesOrdersOrganization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kBackupOrdersDesOrdersOrganization->id), 'class'=>'btn btn-danger btn-xs']) ?>
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