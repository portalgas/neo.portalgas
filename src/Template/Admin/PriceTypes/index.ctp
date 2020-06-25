<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Price Types

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
                  <th scope="col"><?= $this->Paginator->sort('order_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('value') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($priceTypes as $priceType): ?>
                <tr>
                  <td><?= $this->Number->format($priceType->id) ?></td>
                  <td><?= $priceType->has('organization') ? $this->Html->link($priceType->organization->name, ['controller' => 'Organizations', 'action' => 'view', $priceType->organization->id]) : '' ?></td>
                  <td><?= $priceType->has('order') ? $this->Html->link($priceType->order->id, ['controller' => 'Orders', 'action' => 'view', $priceType->order->id]) : '' ?></td>
                  <td><?= h($priceType->code) ?></td>
                  <td><?= h($priceType->name) ?></td>
                  <td><?= h($priceType->type) ?></td>
                  <td><?= $this->Number->format($priceType->value) ?></td>
                  <td><?= h($priceType->is_system) ?></td>
                  <td><?= h($priceType->is_active) ?></td>
                  <td><?= $this->Number->format($priceType->sort) ?></td>
                  <td><?= h($priceType->created) ?></td>
                  <td><?= h($priceType->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $priceType->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $priceType->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $priceType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $priceType->id), 'class'=>'btn btn-danger btn-xs']) ?>
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