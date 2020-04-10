<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Templates Orders States

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
                  <th scope="col"><?= $this->Paginator->sort('template_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('group_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('action_controller') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('action_action') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('flag_menu') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kTemplatesOrdersStates as $kTemplatesOrdersState): ?>
                <tr>
                  <td><?= $kTemplatesOrdersState->has('template') ? $this->Html->link($kTemplatesOrdersState->template->name, ['controller' => 'Templates', 'action' => 'view', $kTemplatesOrdersState->template->id]) : '' ?></td>
                  <td><?= h($kTemplatesOrdersState->state_code) ?></td>
                  <td><?= $this->Number->format($kTemplatesOrdersState->group_id) ?></td>
                  <td><?= h($kTemplatesOrdersState->action_controller) ?></td>
                  <td><?= h($kTemplatesOrdersState->action_action) ?></td>
                  <td><?= h($kTemplatesOrdersState->flag_menu) ?></td>
                  <td><?= $this->Number->format($kTemplatesOrdersState->sort) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kTemplatesOrdersState->template_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kTemplatesOrdersState->template_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kTemplatesOrdersState->template_id], ['confirm' => __('Are you sure you want to delete # {0}?', $kTemplatesOrdersState->template_id), 'class'=>'btn btn-danger btn-xs']) ?>
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