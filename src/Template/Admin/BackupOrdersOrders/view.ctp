<section class="content-header">
  <h1>
    K Backup Orders Order
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
            <dd><?= $kBackupOrdersOrder->has('organization') ? $this->Html->link($kBackupOrdersOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kBackupOrdersOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Owner Articles') ?></dt>
            <dd><?= h($kBackupOrdersOrder->owner_articles) ?></dd>
            <dt scope="row"><?= __('Owner Organization') ?></dt>
            <dd><?= $kBackupOrdersOrder->has('owner_organization') ? $this->Html->link($kBackupOrdersOrder->owner_organization->name, ['controller' => 'OwnerOrganizations', 'action' => 'view', $kBackupOrdersOrder->owner_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Owner Supplier Organization') ?></dt>
            <dd><?= $kBackupOrdersOrder->has('owner_supplier_organization') ? $this->Html->link($kBackupOrdersOrder->owner_supplier_organization->name, ['controller' => 'OwnerSupplierOrganizations', 'action' => 'view', $kBackupOrdersOrder->owner_supplier_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $kBackupOrdersOrder->has('delivery') ? $this->Html->link($kBackupOrdersOrder->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kBackupOrdersOrder->delivery->id]) : '' ?></dd>
            <dt scope="row"><?= __('Prod Gas Promotion') ?></dt>
            <dd><?= $kBackupOrdersOrder->has('prod_gas_promotion') ? $this->Html->link($kBackupOrdersOrder->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kBackupOrdersOrder->prod_gas_promotion->id]) : '' ?></dd>
            <dt scope="row"><?= __('Des Order') ?></dt>
            <dd><?= $kBackupOrdersOrder->has('des_order') ? $this->Html->link($kBackupOrdersOrder->des_order->id, ['controller' => 'DesOrders', 'action' => 'view', $kBackupOrdersOrder->des_order->id]) : '' ?></dd>
            <dt scope="row"><?= __('HasTrasport') ?></dt>
            <dd><?= h($kBackupOrdersOrder->hasTrasport) ?></dd>
            <dt scope="row"><?= __('Trasport Type') ?></dt>
            <dd><?= h($kBackupOrdersOrder->trasport_type) ?></dd>
            <dt scope="row"><?= __('HasCostMore') ?></dt>
            <dd><?= h($kBackupOrdersOrder->hasCostMore) ?></dd>
            <dt scope="row"><?= __('Cost More Type') ?></dt>
            <dd><?= h($kBackupOrdersOrder->cost_more_type) ?></dd>
            <dt scope="row"><?= __('HasCostLess') ?></dt>
            <dd><?= h($kBackupOrdersOrder->hasCostLess) ?></dd>
            <dt scope="row"><?= __('Cost Less Type') ?></dt>
            <dd><?= h($kBackupOrdersOrder->cost_less_type) ?></dd>
            <dt scope="row"><?= __('TypeGest') ?></dt>
            <dd><?= h($kBackupOrdersOrder->typeGest) ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($kBackupOrdersOrder->state_code) ?></dd>
            <dt scope="row"><?= __('Mail Open Send') ?></dt>
            <dd><?= h($kBackupOrdersOrder->mail_open_send) ?></dd>
            <dt scope="row"><?= __('Type Draw') ?></dt>
            <dd><?= h($kBackupOrdersOrder->type_draw) ?></dd>
            <dt scope="row"><?= __('Qta Massima Um') ?></dt>
            <dd><?= h($kBackupOrdersOrder->qta_massima_um) ?></dd>
            <dt scope="row"><?= __('Send Mail Qta Massima') ?></dt>
            <dd><?= h($kBackupOrdersOrder->send_mail_qta_massima) ?></dd>
            <dt scope="row"><?= __('Send Mail Importo Massimo') ?></dt>
            <dd><?= h($kBackupOrdersOrder->send_mail_importo_massimo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Doc1') ?></dt>
            <dd><?= h($kBackupOrdersOrder->tesoriere_doc1) ?></dd>
            <dt scope="row"><?= __('Tesoriere Stato Pay') ?></dt>
            <dd><?= h($kBackupOrdersOrder->tesoriere_stato_pay) ?></dd>
            <dt scope="row"><?= __('Inviato Al Tesoriere Da') ?></dt>
            <dd><?= h($kBackupOrdersOrder->inviato_al_tesoriere_da) ?></dd>
            <dt scope="row"><?= __('IsVisibleFrontEnd') ?></dt>
            <dd><?= h($kBackupOrdersOrder->isVisibleFrontEnd) ?></dd>
            <dt scope="row"><?= __('IsVisibleBackOffice') ?></dt>
            <dd><?= h($kBackupOrdersOrder->isVisibleBackOffice) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->id) ?></dd>
            <dt scope="row"><?= __('Supplier Organization Id') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->supplier_organization_id) ?></dd>
            <dt scope="row"><?= __('Trasport') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->trasport) ?></dd>
            <dt scope="row"><?= __('Cost More') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->cost_more) ?></dd>
            <dt scope="row"><?= __('Cost Less') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->cost_less) ?></dd>
            <dt scope="row"><?= __('Tot Importo') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->tot_importo) ?></dd>
            <dt scope="row"><?= __('Qta Massima') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->qta_massima) ?></dd>
            <dt scope="row"><?= __('Importo Massimo') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->importo_massimo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Fattura Importo') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->tesoriere_fattura_importo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Importo Pay') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersOrder->tesoriere_importo_pay) ?></dd>
            <dt scope="row"><?= __('Data Inizio') ?></dt>
            <dd><?= h($kBackupOrdersOrder->data_inizio) ?></dd>
            <dt scope="row"><?= __('Data Fine') ?></dt>
            <dd><?= h($kBackupOrdersOrder->data_fine) ?></dd>
            <dt scope="row"><?= __('Data Fine Validation') ?></dt>
            <dd><?= h($kBackupOrdersOrder->data_fine_validation) ?></dd>
            <dt scope="row"><?= __('Data Incoming Order') ?></dt>
            <dd><?= h($kBackupOrdersOrder->data_incoming_order) ?></dd>
            <dt scope="row"><?= __('Data State Code Close') ?></dt>
            <dd><?= h($kBackupOrdersOrder->data_state_code_close) ?></dd>
            <dt scope="row"><?= __('Mail Open Data') ?></dt>
            <dd><?= h($kBackupOrdersOrder->mail_open_data) ?></dd>
            <dt scope="row"><?= __('Mail Close Data') ?></dt>
            <dd><?= h($kBackupOrdersOrder->mail_close_data) ?></dd>
            <dt scope="row"><?= __('Tesoriere Data Pay') ?></dt>
            <dd><?= h($kBackupOrdersOrder->tesoriere_data_pay) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kBackupOrdersOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kBackupOrdersOrder->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($kBackupOrdersOrder->nota); ?>
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
            <?= $this->Text->autoParagraph($kBackupOrdersOrder->mail_open_testo); ?>
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
            <?= $this->Text->autoParagraph($kBackupOrdersOrder->tesoriere_nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
