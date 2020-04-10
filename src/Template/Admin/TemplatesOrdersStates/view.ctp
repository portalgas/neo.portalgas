<section class="content-header">
  <h1>
    K Templates Orders State
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
            <dt scope="row"><?= __('Template') ?></dt>
            <dd><?= $kTemplatesOrdersState->has('template') ? $this->Html->link($kTemplatesOrdersState->template->name, ['controller' => 'Templates', 'action' => 'view', $kTemplatesOrdersState->template->id]) : '' ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($kTemplatesOrdersState->state_code) ?></dd>
            <dt scope="row"><?= __('Action Controller') ?></dt>
            <dd><?= h($kTemplatesOrdersState->action_controller) ?></dd>
            <dt scope="row"><?= __('Action Action') ?></dt>
            <dd><?= h($kTemplatesOrdersState->action_action) ?></dd>
            <dt scope="row"><?= __('Flag Menu') ?></dt>
            <dd><?= h($kTemplatesOrdersState->flag_menu) ?></dd>
            <dt scope="row"><?= __('Group Id') ?></dt>
            <dd><?= $this->Number->format($kTemplatesOrdersState->group_id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($kTemplatesOrdersState->sort) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
