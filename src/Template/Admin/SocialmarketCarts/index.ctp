<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];
?>
<section class="content-header">
  <h1>
    Socialmarket Carts

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
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('user_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('article_prezzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cart_qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cart_importo_finale') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($socialmarketCarts as $socialmarketCart): ?>
                <tr>
                  <td><?= $this->Number->format($socialmarketCart->id) ?></td>
                  <td><?= $socialmarketCart->has('organization') ? $this->Html->link($socialmarketCart->organization->name, ['controller' => 'Organizations', 'action' => 'view', $socialmarketCart->organization->id]) : '' ?></td>
                  <td><?= $socialmarketCart->has('user') ? $this->Html->link($socialmarketCart->user->name, ['controller' => 'Users', 'action' => 'view', $socialmarketCart->user->id]) : '' ?></td>
                  <td><?= $this->Number->format($socialmarketCart->user_organization_id) ?></td>
                  <td><?= $socialmarketCart->has('order') ? $this->Html->link($socialmarketCart->order->id, ['controller' => 'Orders', 'action' => 'view', $socialmarketCart->order->organization_id]) : '' ?></td>
                  <td><?= h($socialmarketCart->article_name) ?></td>
                  <td><?= $this->Number->format($socialmarketCart->article_prezzo) ?></td>
                  <td><?= $this->Number->format($socialmarketCart->cart_qta) ?></td>
                  <td><?= $this->Number->format($socialmarketCart->cart_importo_finale) ?></td>
                  <td><?= h($socialmarketCart->is_active) ?></td>
                  <td><?= h($socialmarketCart->created) ?></td>
                  <td><?= h($socialmarketCart->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $socialmarketCart->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $socialmarketCart->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $socialmarketCart->id], ['confirm' => __('Are you sure you want to delete # {0}?', $socialmarketCart->id), 'class'=>'btn btn-danger btn-xs']) ?>
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