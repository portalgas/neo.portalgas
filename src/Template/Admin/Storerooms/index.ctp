<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Storerooms

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
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prezzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kStorerooms as $kStoreroom): ?>
                <tr>
                  <td><?= $this->Number->format($kStoreroom->id) ?></td>
                  <td><?= $kStoreroom->has('organization') ? $this->Html->link($kStoreroom->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStoreroom->organization->id]) : '' ?></td>
                  <td><?= $kStoreroom->has('delivery') ? $this->Html->link($kStoreroom->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kStoreroom->delivery->organization_id]) : '' ?></td>
                  <td><?= $kStoreroom->has('user') ? $this->Html->link($kStoreroom->user->name, ['controller' => 'Users', 'action' => 'view', $kStoreroom->user->id]) : '' ?></td>
                  <td><?= $kStoreroom->has('article') ? $this->Html->link($kStoreroom->article->name, ['controller' => 'Articles', 'action' => 'view', $kStoreroom->article->organization_id]) : '' ?></td>
                  <td><?= $this->Number->format($kStoreroom->article_organization_id) ?></td>
                  <td><?= h($kStoreroom->name) ?></td>
                  <td><?= $this->Number->format($kStoreroom->qta) ?></td>
                  <td><?= $this->Number->format($kStoreroom->prezzo) ?></td>
                  <td><?= h($kStoreroom->stato) ?></td>
                  <td><?= h($kStoreroom->created) ?></td>
                  <td><?= h($kStoreroom->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kStoreroom->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kStoreroom->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kStoreroom->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kStoreroom->id), 'class'=>'btn btn-danger btn-xs']) ?>
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