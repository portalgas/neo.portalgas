<section class="content-header">
  <h1>
    K Cashes History
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
            <dd><?= $kCashesHistory->has('organization') ? $this->Html->link($kCashesHistory->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kCashesHistory->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Cash') ?></dt>
            <dd><?= $kCashesHistory->has('cash') ? $this->Html->link($kCashesHistory->cash->id, ['controller' => 'Cashes', 'action' => 'view', $kCashesHistory->cash->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kCashesHistory->has('user') ? $this->Html->link($kCashesHistory->user->name, ['controller' => 'Users', 'action' => 'view', $kCashesHistory->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kCashesHistory->id) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kCashesHistory->importo) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kCashesHistory->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kCashesHistory->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Nota') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kCashesHistory->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
