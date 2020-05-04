<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Stat Carts

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
                  <th scope="col"><?= $this->Paginator->sort('article_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stat_order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kStatCarts as $kStatCart): ?>
                <tr>
                  <td><?= $kStatCart->has('organization') ? $this->Html->link($kStatCart->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatCart->organization->id]) : '' ?></td>
                  <td><?= $kStatCart->has('user') ? $this->Html->link($kStatCart->user->name, ['controller' => 'Users', 'action' => 'view', $kStatCart->user->id]) : '' ?></td>
                  <td><?= $this->Number->format($kStatCart->article_organization_id) ?></td>
                  <td><?= $kStatCart->has('article') ? $this->Html->link($kStatCart->article->name, ['controller' => 'Articles', 'action' => 'view', $kStatCart->article->id]) : '' ?></td>
                  <td><?= $this->Number->format($kStatCart->stat_order_id) ?></td>
                  <td><?= $this->Number->format($kStatCart->qta) ?></td>
                  <td><?= $this->Number->format($kStatCart->importo) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kStatCart->organization_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kStatCart->organization_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kStatCart->organization_id], ['confirm' => __('Are you sure you want to delete # {0}?', $kStatCart->organization_id), 'class'=>'btn btn-danger btn-xs']) ?>
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