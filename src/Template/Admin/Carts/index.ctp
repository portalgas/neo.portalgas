<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Carts

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
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('deleteToReferent') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_forzato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_forzato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('inStoreroom') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('date') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kCarts as $kCart): ?>
                <tr>
                  <td><?= $this->Number->format($kCart->organization_id) ?></td>
                  <td><?= $this->Number->format($kCart->user_id) ?></td>
                  <td><?= $this->Number->format($kCart->order_id) ?></td>
                  <td><?= $this->Number->format($kCart->article_organization_id) ?></td>
                  <td><?= $this->Number->format($kCart->article_id) ?></td>
                  <td><?= $this->Number->format($kCart->qta) ?></td>
                  <td><?= h($kCart->deleteToReferent) ?></td>
                  <td><?= $this->Number->format($kCart->qta_forzato) ?></td>
                  <td><?= $this->Number->format($kCart->importo_forzato) ?></td>
                  <td><?= h($kCart->inStoreroom) ?></td>
                  <td><?= h($kCart->stato) ?></td>
                  <td><?= h($kCart->created) ?></td>
                  <td><?= h($kCart->date) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kCart->organization_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kCart->organization_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kCart->organization_id], ['confirm' => __('Are you sure you want to delete # {0}?', $kCart->organization_id), 'class'=>'btn btn-danger btn-xs']) ?>
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