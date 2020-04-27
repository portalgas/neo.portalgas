<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Tables

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
                  <th scope="col"><?= $this->Paginator->sort('scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('table_name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('entity') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('where_key') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('update_key') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tables as $table): ?>
                <tr>
                  <td><?= $this->Number->format($table->id) ?></td>
                  <td><?= $table->has('scope') ? $this->Html->link($table->scope->name, ['controller' => 'Scopes', 'action' => 'view', $table->scope->id]) : '' ?></td>
                  <td><?= h($table->name) ?></td>
                  <td><?= h($table->table_name) ?></td>
                  <td><?= h($table->entity) ?></td>
                  <td><?= h($table->where_key) ?></td>
                  <td><?= h($table->update_key) ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($table, $table->is_system); ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($table, $table->is_active); ?></td>
                  <td><?= h($table->created) ?></td>
                  <td><?= h($table->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $table->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $table->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $table->id], ['confirm' => __('Are you sure you want to delete # {0}?', $table->id), 'class'=>'btn btn-danger btn-xs']) ?>
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