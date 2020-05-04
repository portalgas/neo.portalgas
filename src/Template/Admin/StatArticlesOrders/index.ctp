<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Stat Articles Orders

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
                  <th scope="col"><?= $this->Paginator->sort('stat_order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('codice') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prezzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('um') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('um_riferimento') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kStatArticlesOrders as $kStatArticlesOrder): ?>
                <tr>
                  <td><?= $kStatArticlesOrder->has('organization') ? $this->Html->link($kStatArticlesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatArticlesOrder->organization->id]) : '' ?></td>
                  <td><?= $this->Number->format($kStatArticlesOrder->stat_order_id) ?></td>
                  <td><?= $this->Number->format($kStatArticlesOrder->article_organization_id) ?></td>
                  <td><?= $kStatArticlesOrder->has('article') ? $this->Html->link($kStatArticlesOrder->article->name, ['controller' => 'Articles', 'action' => 'view', $kStatArticlesOrder->article->id]) : '' ?></td>
                  <td><?= h($kStatArticlesOrder->name) ?></td>
                  <td><?= h($kStatArticlesOrder->codice) ?></td>
                  <td><?= $this->Number->format($kStatArticlesOrder->prezzo) ?></td>
                  <td><?= $this->Number->format($kStatArticlesOrder->qta) ?></td>
                  <td><?= h($kStatArticlesOrder->um) ?></td>
                  <td><?= h($kStatArticlesOrder->um_riferimento) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kStatArticlesOrder->organization_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kStatArticlesOrder->organization_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kStatArticlesOrder->organization_id], ['confirm' => __('Are you sure you want to delete # {0}?', $kStatArticlesOrder->organization_id), 'class'=>'btn btn-danger btn-xs']) ?>
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