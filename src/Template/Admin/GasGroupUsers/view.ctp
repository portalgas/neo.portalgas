<section class="content-header">
  <h1>
    Gas Group User
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
            <dd><?= $gasGroupUser->has('organization') ? $this->Html->link($gasGroupUser->organization->name, ['controller' => 'Organizations', 'action' => 'view', $gasGroupUser->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $gasGroupUser->has('user') ? $this->Html->link($gasGroupUser->user->name, ['controller' => 'Users', 'action' => 'view', $gasGroupUser->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Gas Group') ?></dt>
            <dd><?= $gasGroupUser->has('gas_group') ? $this->Html->link($gasGroupUser->gas_group->name, ['controller' => 'GasGroups', 'action' => 'view', $gasGroupUser->gas_group->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($gasGroupUser->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($gasGroupUser->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($gasGroupUser->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
