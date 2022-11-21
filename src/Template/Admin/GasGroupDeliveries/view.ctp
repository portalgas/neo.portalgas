<section class="content-header">
  <h1>
    Gas Group Delivery
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
            <dd><?= $gasGroupDelivery->has('organization') ? $this->Html->link($gasGroupDelivery->organization->name, ['controller' => 'Organizations', 'action' => 'view', $gasGroupDelivery->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $gasGroupDelivery->has('delivery') ? $this->Html->link($gasGroupDelivery->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $gasGroupDelivery->delivery->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Luogo') ?></dt>
            <dd><?= h($gasGroupDelivery->luogo) ?></dd>
            <dt scope="row"><?= __('Nota Evidenza') ?></dt>
            <dd><?= h($gasGroupDelivery->nota_evidenza) ?></dd>
            <dt scope="row"><?= __('Stato Elaborazione') ?></dt>
            <dd><?= h($gasGroupDelivery->stato_elaborazione) ?></dd>
            <dt scope="row"><?= __('Gcalendar Event Id') ?></dt>
            <dd><?= h($gasGroupDelivery->gcalendar_event_id) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($gasGroupDelivery->id) ?></dd>
            <dt scope="row"><?= __('Data') ?></dt>
            <dd><?= h($gasGroupDelivery->data) ?></dd>
            <dt scope="row"><?= __('Orario Da') ?></dt>
            <dd><?= h($gasGroupDelivery->orario_da) ?></dd>
            <dt scope="row"><?= __('Orario A') ?></dt>
            <dd><?= h($gasGroupDelivery->orario_a) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($gasGroupDelivery->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($gasGroupDelivery->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($gasGroupDelivery->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
