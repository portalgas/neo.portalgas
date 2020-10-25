<section class="content-header">
  <h1>
    Order State Code
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
            <dd><?= h($orderStateCode->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($orderStateCode->name) ?></dd>
            <dt scope="row"><?= __('Css Color') ?></dt>
            <dd><?= h($orderStateCode->css_color) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($orderStateCode->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($orderStateCode->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($orderStateCode->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($orderStateCode->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $orderStateCode->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $orderStateCode->is_active ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default Ini') ?></dt>
            <dd><?= $orderStateCode->is_default_ini ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Default End') ?></dt>
            <dd><?= $orderStateCode->is_default_end ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($orderStateCode->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
