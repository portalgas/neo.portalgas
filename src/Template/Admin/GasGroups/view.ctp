<section class="content-header">
  <h1>
    Gas Group
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
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $gasGroup->has('organization') ? $this->Html->link($gasGroup->organization->name, ['controller' => 'Organizations', 'action' => 'view', $gasGroup->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $gasGroup->has('user') ? $this->Html->link($gasGroup->user->name, ['controller' => 'Users', 'action' => 'view', $gasGroup->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($gasGroup->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($gasGroup->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($gasGroup->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($gasGroup->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $gasGroup->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $gasGroup->is_active ? __('Yes') : __('No'); ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Descri') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($gasGroup->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Gas Group Deliveries') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($gasGroup->gas_group_deliveries)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('Gas Group Id') ?></th>
                    <th scope="col"><?= __('Delivery Id') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($gasGroup->gas_group_deliveries as $gasGroupDeliveries): ?>
              <tr>
                    <td><?= h($gasGroupDeliveries->id) ?></td>
                    <td><?= h($gasGroupDeliveries->organization_id) ?></td>
                    <td><?= h($gasGroupDeliveries->gas_group_id) ?></td>
                    <td><?= h($gasGroupDeliveries->delivery_id) ?></td>
                    <td><?= h($gasGroupDeliveries->created) ?></td>
                    <td><?= h($gasGroupDeliveries->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'GasGroupDeliveries', 'action' => 'view', $gasGroupDeliveries->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'GasGroupDeliveries', 'action' => 'edit', $gasGroupDeliveries->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'GasGroupDeliveries', 'action' => 'delete', $gasGroupDeliveries->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gasGroupDeliveries->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Gas Group Users') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($gasGroup->gas_group_users)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('User Id') ?></th>
                    <th scope="col"><?= __('Gas Group Id') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($gasGroup->gas_group_users as $gasGroupUsers): ?>
              <tr>
                    <td><?= h($gasGroupUsers->id) ?></td>
                    <td><?= h($gasGroupUsers->organization_id) ?></td>
                    <td><?= h($gasGroupUsers->user_id) ?></td>
                    <td><?= h($gasGroupUsers->gas_group_id) ?></td>
                    <td><?= h($gasGroupUsers->created) ?></td>
                    <td><?= h($gasGroupUsers->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'GasGroupUsers', 'action' => 'view', $gasGroupUsers->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'GasGroupUsers', 'action' => 'edit', $gasGroupUsers->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'GasGroupUsers', 'action' => 'delete', $gasGroupUsers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gasGroupUsers->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
