<section class="content-header">
  <h1>
    Orders Type
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
            <dt scope="row"><?= __('Code') ?></dt>
            <dd><?= h($ordersType->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($ordersType->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($ordersType->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($ordersType->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($ordersType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($ordersType->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $ordersType->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $ordersType->is_active ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default Ini') ?></dt>
            <dd><?= $ordersType->is_default_ini ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default End') ?></dt>
            <dd><?= $ordersType->is_default_end ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($ordersType->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
