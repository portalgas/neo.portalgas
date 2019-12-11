<section class="content-header">
  <h1>
    Scope
    <small><?php echo __('View'); ?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-info"></i>
          <h3 class="box-title"><?php echo __('Information'); ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <dl class="dl-horizontal">
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($scope->name) ?></dd>
            <dt scope="row"><?= __('Namespace') ?></dt>
            <dd><?= h($scope->namespace) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($scope->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($scope->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($scope->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $scope->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $scope->is_active ? __('Yes') : __('No'); ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Tables') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($scope->tables)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Scope Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Table Name') ?></th>
                    <th scope="col"><?= __('Entity') ?></th>
                    <th scope="col"><?= __('Where Key') ?></th>
                    <th scope="col"><?= __('Is System') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($scope->tables as $tables): ?>
              <tr>
                    <td><?= h($tables->id) ?></td>
                    <td><?= h($tables->scope_id) ?></td>
                    <td><?= h($tables->name) ?></td>
                    <td><?= h($tables->table_name) ?></td>
                    <td><?= h($tables->entity) ?></td>
                    <td><?= h($tables->where_key) ?></td>
                    <td><?= h($tables->is_system) ?></td>
                    <td><?= h($tables->is_active) ?></td>
                    <td><?= h($tables->created) ?></td>
                    <td><?= h($tables->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Tables', 'action' => 'view', $tables->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Tables', 'action' => 'edit', $tables->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Tables', 'action' => 'delete', $tables->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tables->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
