<section class="content-header">
  <h1>
    K Stat Order
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
            <dd><?= $kStatOrder->has('organization') ? $this->Html->link($kStatOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Supplier Organization Name') ?></dt>
            <dd><?= h($kStatOrder->supplier_organization_name) ?></dd>
            <dt scope="row"><?= __('Supplier Img1') ?></dt>
            <dd><?= h($kStatOrder->supplier_img1) ?></dd>
            <dt scope="row"><?= __('Tesoriere Doc1') ?></dt>
            <dd><?= h($kStatOrder->tesoriere_doc1) ?></dd>
            <dt scope="row"><?= __('Request Payment Num') ?></dt>
            <dd><?= h($kStatOrder->request_payment_num) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->id) ?></dd>
            <dt scope="row"><?= __('Supplier Organization Id') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->supplier_organization_id) ?></dd>
            <dt scope="row"><?= __('Stat Delivery Id') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->stat_delivery_id) ?></dd>
            <dt scope="row"><?= __('Stat Delivery Year') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->stat_delivery_year) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->importo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Fattura Importo') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->tesoriere_fattura_importo) ?></dd>
            <dt scope="row"><?= __('Tesoriere Importo Pay') ?></dt>
            <dd><?= $this->Number->format($kStatOrder->tesoriere_importo_pay) ?></dd>
            <dt scope="row"><?= __('Data Inizio') ?></dt>
            <dd><?= h($kStatOrder->data_inizio) ?></dd>
            <dt scope="row"><?= __('Data Fine') ?></dt>
            <dd><?= h($kStatOrder->data_fine) ?></dd>
            <dt scope="row"><?= __('Tesoriere Data Pay') ?></dt>
            <dd><?= h($kStatOrder->tesoriere_data_pay) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
