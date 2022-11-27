<section class="content-header">
  <h1>
    K Delivery
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
            <dd><?= $delivery->has('organization') ? $this->Html->link($delivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $delivery->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Luogo') ?></dt>
            <dd><?= h($delivery->luogo) ?></dd>
            <dt scope="row"><?= __('Nota Evidenza') ?></dt>
            <dd><?= h($delivery->nota_evidenza) ?></dd>
            <dt scope="row"><?= __('IsToStoreroom') ?></dt>
            <dd><?= h($delivery->isToStoreroom) ?></dd>
            <dt scope="row"><?= __('IsToStoreroomPay') ?></dt>
            <dd><?= h($delivery->isToStoreroomPay) ?></dd>
            <dt scope="row"><?= __('Stato Elaborazione') ?></dt>
            <dd><?= h($delivery->stato_elaborazione) ?></dd>
            <dt scope="row"><?= __('IsVisibleFrontEnd') ?></dt>
            <dd><?= h($delivery->isVisibleFrontEnd) ?></dd>
            <dt scope="row"><?= __('IsVisibleBackOffice') ?></dt>
            <dd><?= h($delivery->isVisibleBackOffice) ?></dd>
            <dt scope="row"><?= __('Sys') ?></dt>
            <dd><?= h($delivery->sys) ?></dd>
            <dt scope="row"><?= __('Gcalendar Event Id') ?></dt>
            <dd><?= h($delivery->gcalendar_event_id) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($delivery->id) ?></dd>
            <dt scope="row"><?= __('Data') ?></dt>
            <dd><?= h($delivery->data) ?></dd>
            <dt scope="row"><?= __('Orario Da') ?></dt>
            <dd><?= h($delivery->orario_da) ?></dd>
            <dt scope="row"><?= __('Orario A') ?></dt>
            <dd><?= h($delivery->orario_a) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($delivery->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($delivery->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($delivery->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
