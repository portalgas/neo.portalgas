<section class="content-header">
  <h1>
    Movement Type
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
            <dd><?= h($movementType->name) ?></dd>
            <dt scope="row"><?= __('Model') ?></dt>
            <dd><?= h($movementType->model) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($movementType->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($movementType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($movementType->modified) ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $movementType->is_active ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $movementType->is_system ? __('Yes') : __('No'); ?></dd>
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
          <h3 class="box-title"><?= __('Movements') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($movementType->movements)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('Movement Type Id') ?></th>
                    <th scope="col"><?= __('User Id') ?></th>
                    <th scope="col"><?= __('Supplier Organization Id') ?></th>
                    <th scope="col"><?= __('Year') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Descri') ?></th>
                    <th scope="col"><?= __('Importo') ?></th>
                    <th scope="col"><?= __('Date') ?></th>
                    <th scope="col"><?= __('Type') ?></th>
                    <th scope="col"><?= __('Is System') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($movementType->movements as $movements): ?>
              <tr>
                    <td><?= h($movements->id) ?></td>
                    <td><?= h($movements->organization_id) ?></td>
                    <td><?= h($movements->movement_type_id) ?></td>
                    <td><?= h($movements->user_id) ?></td>
                    <td><?= h($movements->supplier_organization_id) ?></td>
                    <td><?= h($movements->year) ?></td>
                    <td><?= h($movements->name) ?></td>
                    <td><?= h($movements->descri) ?></td>
                    <td><?= h($movements->importo) ?></td>
                    <td><?= h($movements->date) ?></td>
                    <td><?= h($movements->type) ?></td>
                    <td><?= h($movements->is_system) ?></td>
                    <td><?= h($movements->is_active) ?></td>
                    <td><?= h($movements->created) ?></td>
                    <td><?= h($movements->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Movements', 'action' => 'view', $movements->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Movements', 'action' => 'edit', $movements->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Movements', 'action' => 'delete', $movements->id], ['confirm' => __('Are you sure you want to delete # {0}?', $movements->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
