<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Orders

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
                  <th scope="col"><?= $this->Paginator->sort('owner_articles') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prod_gas_promotion_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('des_order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_inizio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine_validation') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_incoming_order') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_state_code_close') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasTrasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('trasport_type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('trasport') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasCostMore') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_more_type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_more') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasCostLess') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_less_type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cost_less') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('typeGest') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mail_open_send') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mail_open_data') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mail_close_data') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('type_draw') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima_um') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('send_mail_qta_massima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_massimo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('send_mail_importo_massimo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_fattura_importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_doc1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_data_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_importo_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tesoriere_stato_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('inviato_al_tesoriere_da') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isVisibleFrontEnd') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('isVisibleBacoffice') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td><?= $this->Number->format($order->id) ?></td>
                  <td><?= $order->has('organization') ? $this->Html->link($order->organization->name, ['controller' => 'Organizations', 'action' => 'view', $order->organization->id]) : '' ?></td>
                  <td><?= $this->Number->format($order->supplier_organization_id) ?></td>
                  <td><?= h($order->owner_articles) ?></td>
                  <td><?= $order->has('owner_organization') ? $this->Html->link($order->owner_organization->name, ['controller' => 'OwnerOrganizations', 'action' => 'view', $order->owner_organization->id]) : '' ?></td>
                  <td><?= $order->has('owner_supplier_organization') ? $this->Html->link($order->owner_supplier_organization->name, ['controller' => 'OwnerSupplierOrganizations', 'action' => 'view', $order->owner_supplier_organization->id]) : '' ?></td>
                  <td><?= $order->has('delivery') ? $this->Html->link($order->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $order->delivery->id]) : '' ?></td>
                  <td><?= $this->Number->format($order->prod_gas_promotion_id) ?></td>
                  <td><?= $this->Number->format($order->des_order_id) ?></td>
                  <td><?= h($order->data_inizio) ?></td>
                  <td><?= h($order->data_fine) ?></td>
                  <td><?= h($order->data_fine_validation) ?></td>
                  <td><?= h($order->data_incoming_order) ?></td>
                  <td><?= h($order->data_state_code_close) ?></td>
                  <td><?= h($order->hasTrasport) ?></td>
                  <td><?= h($order->trasport_type) ?></td>
                  <td><?= $this->Number->format($order->trasport) ?></td>
                  <td><?= h($order->hasCostMore) ?></td>
                  <td><?= h($order->cost_more_type) ?></td>
                  <td><?= $this->Number->format($order->cost_more) ?></td>
                  <td><?= h($order->hasCostLess) ?></td>
                  <td><?= h($order->cost_less_type) ?></td>
                  <td><?= $this->Number->format($order->cost_less) ?></td>
                  <td><?= h($order->typeGest) ?></td>
                  <td><?= h($order->state_code) ?></td>
                  <td><?= h($order->mail_open_send) ?></td>
                  <td><?= h($order->mail_open_data) ?></td>
                  <td><?= h($order->mail_close_data) ?></td>
                  <td><?= h($order->type_draw) ?></td>
                  <td><?= $this->Number->format($order->tot_importo) ?></td>
                  <td><?= $this->Number->format($order->qta_massima) ?></td>
                  <td><?= h($order->qta_massima_um) ?></td>
                  <td><?= h($order->send_mail_qta_massima) ?></td>
                  <td><?= $this->Number->format($order->importo_massimo) ?></td>
                  <td><?= h($order->send_mail_importo_massimo) ?></td>
                  <td><?= $this->Number->format($order->tesoriere_fattura_importo) ?></td>
                  <td><?= h($order->tesoriere_doc1) ?></td>
                  <td><?= h($order->tesoriere_data_pay) ?></td>
                  <td><?= $this->Number->format($order->tesoriere_importo_pay) ?></td>
                  <td><?= h($order->tesoriere_stato_pay) ?></td>
                  <td><?= h($order->inviato_al_tesoriere_da) ?></td>
                  <td><?= h($order->isVisibleFrontEnd) ?></td>
                  <td><?= h($order->isVisibleBacoffice) ?></td>
                  <td><?= h($order->created) ?></td>
                  <td><?= h($order->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $order->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $order->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class'=>'btn btn-danger btn-xs']) ?>
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