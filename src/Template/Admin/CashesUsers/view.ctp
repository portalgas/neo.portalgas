<section class="content-header">
  <h1>
    K Cashes User
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
            <dd><?= $kCashesUser->has('organization') ? $this->Html->link($kCashesUser->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kCashesUser->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kCashesUser->has('user') ? $this->Html->link($kCashesUser->user->name, ['controller' => 'Users', 'action' => 'view', $kCashesUser->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Limit Type') ?></dt>
            <dd><?= h($kCashesUser->limit_type) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kCashesUser->id) ?></dd>
            <dt scope="row"><?= __('Limit After') ?></dt>
            <dd><?= $this->Number->format($kCashesUser->limit_after) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kCashesUser->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kCashesUser->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
