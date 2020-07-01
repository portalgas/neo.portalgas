<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Articles Orders

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
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_cart') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prezzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('pezzi_confezione') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_minima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_minima_order') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima_order') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_multipli') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('alert_to_qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('send_mail') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('flag_bookmarks') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($articlesOrders as $articlesOrder): ?>
                <tr>
                  <td><?= $articlesOrder->has('organization') ? $this->Html->link($articlesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $articlesOrder->organization->id]) : '' ?></td>
                  <td><?= $articlesOrder->has('order') ? $this->Html->link($articlesOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $articlesOrder->order->id]) : '' ?></td>
                  <td><?= $articlesOrder->has('article_organization') ? $this->Html->link($articlesOrder->article_organization->name, ['controller' => 'Organizations', 'action' => 'view', $articlesOrder->article_organization->id]) : '' ?></td>
                  <td><?= $articlesOrder->has('article') ? $this->Html->link($articlesOrder->article->name, ['controller' => 'Articles', 'action' => 'view', $articlesOrder->article->id]) : '' ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_cart) ?></td>
                  <td><?= h($articlesOrder->name) ?></td>
                  <td><?= $this->Number->format($articlesOrder->prezzo) ?></td>
                  <td><?= $this->Number->format($articlesOrder->pezzi_confezione) ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_minima) ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_massima) ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_minima_order) ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_massima_order) ?></td>
                  <td><?= $this->Number->format($articlesOrder->qta_multipli) ?></td>
                  <td><?= $this->Number->format($articlesOrder->alert_to_qta) ?></td>
                  <td><?= h($articlesOrder->send_mail) ?></td>
                  <td><?= h($articlesOrder->flag_bookmarks) ?></td>
                  <td><?= h($articlesOrder->stato) ?></td>
                  <td><?= h($articlesOrder->created) ?></td>
                  <td><?= h($articlesOrder->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $articlesOrder->organization_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $articlesOrder->organization_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $articlesOrder->organization_id], ['confirm' => __('Are you sure you want to delete # {0}?', $articlesOrder->organization_id), 'class'=>'btn btn-danger btn-xs']) ?>
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