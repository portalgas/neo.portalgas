<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Order State Codes

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
                  <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('css_color') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_default_ini') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_default_end') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orderStateCodes as $orderStateCode): ?>
                <tr>
                  <td><?= $this->Number->format($orderStateCode->id) ?></td>
                  <td><?= h($orderStateCode->code) ?></td>
                  <td><?= h($orderStateCode->name) ?></td>
                  <td><?= h($orderStateCode->css_color) ?></td>
                  <td><?= h($orderStateCode->is_system) ?></td>
                  <td><?= h($orderStateCode->is_active) ?></td>
                  <td><?= h($orderStateCode->is_default_ini) ?></td>
                  <td><?= h($orderStateCode->is_default_end) ?></td>
                  <td><?= $this->Number->format($orderStateCode->sort) ?></td>
                  <td><?= h($orderStateCode->created) ?></td>
                  <td><?= h($orderStateCode->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $orderStateCode->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $orderStateCode->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $orderStateCode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderStateCode->id), 'class'=>'btn btn-danger btn-xs']) ?>
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