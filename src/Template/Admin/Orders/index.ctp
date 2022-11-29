<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?php echo __('Orders');?>
    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add', $order_type_id], ['class'=>'btn btn-success btn-xs']) ?></div>
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
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
                  <th scope="col" colspan="2"><?= $this->Paginator->sort('supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_articles') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_inizio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): 
                ?>
                <tr>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $order->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $order->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                  <?php 
                    echo '<td>';
                    echo $this->HtmlCustomSite->drawSupplierImage($order->suppliers_organization->supplier).'</td>';
                  ?>
                  <td><?= h($order->suppliers_organization->name) ?></td>
                  <td><?= h($order->owner_articles) ?></td>
                  <td><?= $order->has('owner_organization') ? $this->Html->link($order->owner_organization->name, ['controller' => 'OwnerOrganizations', 'action' => 'view', $order->owner_organization->id]) : '' ?></td>
                  <td><?= $order->has('owner_supplier_organization') ? $this->Html->link($order->owner_supplier_organization->name, ['controller' => 'OwnerSupplierOrganizations', 'action' => 'view', $order->owner_supplier_organization->id]) : '' ?></td>
                  <td><?= $order->has('delivery') ? $this->Html->link($order->delivery->luogo, ['controller' => 'Deliveries', 'action' => 'view', $order->delivery->id]) : '' ?></td>
                  <td><?= h($order->data_inizio) ?></td>
                  <td>
                    <?= h($order->data_fine) ?>
                    <?= h($order->data_fine_validation) ?>
                  </td>
                  <td><?= h($order->state_code) ?></td>
                  <td><?= $this->Number->format($order->tot_importo) ?></td>
                  <td><?= h($order->created) ?></td>
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