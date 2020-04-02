<section class="content-header">
  <h1>
    K Order
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
            <dd><?= $order->has('organization') ? $this->Html->link($order->organization->name, ['controller' => 'Organizations', 'action' => 'view', $order->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Owner Articles') ?></dt>
            <dd><?= h($order->owner_articles) ?></dd>
            <dt scope="row"><?= __('Owner Organization') ?></dt>
            <dd><?= $order->has('owner_organization') ? $this->Html->link($order->owner_organization->name, ['controller' => 'OwnerOrganizations', 'action' => 'view', $order->owner_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Owner Supplier Organization') ?></dt>
            <dd><?= $order->has('owner_supplier_organization') ? $this->Html->link($order->owner_supplier_organization->name, ['controller' => 'OwnerSupplierOrganizations', 'action' => 'view', $order->owner_supplier_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $order->has('delivery') ? $this->Html->link($order->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $order->delivery->id]) : '' ?></dd>
            <dt scope="row"><?= __('HasTrasport') ?></dt>
            <dd><?= h($order->hasTrasport) ?></dd>
            <dt scope="row"><?= __('Trasport Type') ?></dt>
            <dd><?= h($order->trasport_type) ?></dd>
            <dt scope="row"><?= __('HasCostMore') ?></dt>
            <dd><?= h($order->hasCostMore) ?></dd>
            <dt scope="row"><?= __('Cost More Type') ?></dt>
            <dd><?= h($order->cost_more_type) ?></dd>
            <dt scope="row"><?= __('HasCostLess') ?></dt>
            <dd><?= h($order->hasCostLess) ?></dd>
            <dt scope="row"><?= __('Cost Less Type') ?></dt>
            <dd><?= h($order->cost_less_type) ?></dd>
            <dt scope="row"><?= __('TypeGest') ?></dt>
            <dd><?= h($order->typeGest) ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($order->state_code) ?></dd>
            <dt scope="row"><?= __('Mail Open Send') ?></dt>
            <dd><?= h($order->mail_open_send) ?></dd>
            <dt scope="row"><?= __('Type Draw') ?></dt>
            <dd><?= h($order->type_draw) ?></dd>
            <dt scope="row"><?= __('Qta Massima Um') ?></dt>
            <dd><?= h($order->qta_massima_um) ?></dd>
            <dt scope="row"><?= __('Send Mail Qta Massima') ?></dt>
            <dd><?= h($order->send_mail_qta_massima) ?></dd>
            <dt scope="row"><?= __('Send Mail Importo Massimo') ?></dt>
            <dd><?= h($order->send_mail_importo_massimo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Doc1') ?></dt>
            <dd><?= h($order->tesoriere_doc1) ?></dd>
            <dt scope="row"><?= __('Tesoriere Stato Pay') ?></dt>
            <dd><?= h($order->tesoriere_stato_pay) ?></dd>
            <dt scope="row"><?= __('Inviato Al Tesoriere Da') ?></dt>
            <dd><?= h($order->inviato_al_tesoriere_da) ?></dd>
            <dt scope="row"><?= __('IsVisibleFrontEnd') ?></dt>
            <dd><?= h($order->isVisibleFrontEnd) ?></dd>
            <dt scope="row"><?= __('IsVisibleBacoffice') ?></dt>
            <dd><?= h($order->isVisibleBacoffice) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($order->id) ?></dd>
            <dt scope="row"><?= __('Supplier Organization Id') ?></dt>
            <dd><?= $this->Number->format($order->supplier_organization_id) ?></dd>
            <dt scope="row"><?= __('Prod Gas Promotion Id') ?></dt>
            <dd><?= $this->Number->format($order->prod_gas_promotion_id) ?></dd>
            <dt scope="row"><?= __('Des Order Id') ?></dt>
            <dd><?= $this->Number->format($order->des_order_id) ?></dd>
            <dt scope="row"><?= __('Trasport') ?></dt>
            <dd><?= $this->Number->format($order->trasport) ?></dd>
            <dt scope="row"><?= __('Cost More') ?></dt>
            <dd><?= $this->Number->format($order->cost_more) ?></dd>
            <dt scope="row"><?= __('Cost Less') ?></dt>
            <dd><?= $this->Number->format($order->cost_less) ?></dd>
            <dt scope="row"><?= __('Tot Importo') ?></dt>
            <dd><?= $this->Number->format($order->tot_importo) ?></dd>
            <dt scope="row"><?= __('Qta Massima') ?></dt>
            <dd><?= $this->Number->format($order->qta_massima) ?></dd>
            <dt scope="row"><?= __('Importo Massimo') ?></dt>
            <dd><?= $this->Number->format($order->importo_massimo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Fattura Importo') ?></dt>
            <dd><?= $this->Number->format($order->tesoriere_fattura_importo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Importo Pay') ?></dt>
            <dd><?= $this->Number->format($order->tesoriere_importo_pay) ?></dd>
            <dt scope="row"><?= __('Data Inizio') ?></dt>
            <dd><?= h($order->data_inizio) ?></dd>
            <dt scope="row"><?= __('Data Fine') ?></dt>
            <dd><?= h($order->data_fine) ?></dd>
            <dt scope="row"><?= __('Data Fine Validation') ?></dt>
            <dd><?= h($order->data_fine_validation) ?></dd>
            <dt scope="row"><?= __('Data Incoming Order') ?></dt>
            <dd><?= h($order->data_incoming_order) ?></dd>
            <dt scope="row"><?= __('Data State Code Close') ?></dt>
            <dd><?= h($order->data_state_code_close) ?></dd>
            <dt scope="row"><?= __('Mail Open Data') ?></dt>
            <dd><?= h($order->mail_open_data) ?></dd>
            <dt scope="row"><?= __('Mail Close Data') ?></dt>
            <dd><?= h($order->mail_close_data) ?></dd>
            <dt scope="row"><?= __('Tesoriere Data Pay') ?></dt>
            <dd><?= h($order->tesoriere_data_pay) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($order->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($order->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($order->nota); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Mail Open Testo') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($order->mail_open_testo); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Tesoriere Nota') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($order->tesoriere_nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
