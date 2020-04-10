<section class="content-header">
  <h1>
    K Template
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
            <dd><?= h($kTemplate->name) ?></dd>
            <dt scope="row"><?= __('PayToDelivery') ?></dt>
            <dd><?= h($kTemplate->payToDelivery) ?></dd>
            <dt scope="row"><?= __('OrderForceClose') ?></dt>
            <dd><?= h($kTemplate->orderForceClose) ?></dd>
            <dt scope="row"><?= __('OrderUserPaid') ?></dt>
            <dd><?= h($kTemplate->orderUserPaid) ?></dd>
            <dt scope="row"><?= __('OrderSupplierPaid') ?></dt>
            <dd><?= h($kTemplate->orderSupplierPaid) ?></dd>
            <dt scope="row"><?= __('HasCassiere') ?></dt>
            <dd><?= h($kTemplate->hasCassiere) ?></dd>
            <dt scope="row"><?= __('HasTesoriere') ?></dt>
            <dd><?= h($kTemplate->hasTesoriere) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kTemplate->id) ?></dd>
            <dt scope="row"><?= __('GgArchiveStatics') ?></dt>
            <dd><?= $this->Number->format($kTemplate->ggArchiveStatics) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kTemplate->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kTemplate->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($kTemplate->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Descri Order Cycle Life') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kTemplate->descri_order_cycle_life); ?>
        </div>
      </div>
    </div>
  </div>
</section>
