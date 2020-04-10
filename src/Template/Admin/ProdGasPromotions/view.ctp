<section class="content-header">
  <h1>
    K Prod Gas Promotion
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
            <dd><?= $kProdGasPromotion->has('organization') ? $this->Html->link($kProdGasPromotion->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotion->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($kProdGasPromotion->name) ?></dd>
            <dt scope="row"><?= __('Img1') ?></dt>
            <dd><?= h($kProdGasPromotion->img1) ?></dd>
            <dt scope="row"><?= __('Contact Name') ?></dt>
            <dd><?= h($kProdGasPromotion->contact_name) ?></dd>
            <dt scope="row"><?= __('Contact Mail') ?></dt>
            <dd><?= h($kProdGasPromotion->contact_mail) ?></dd>
            <dt scope="row"><?= __('Contact Phone') ?></dt>
            <dd><?= h($kProdGasPromotion->contact_phone) ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($kProdGasPromotion->state_code) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($kProdGasPromotion->stato) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotion->id) ?></dd>
            <dt scope="row"><?= __('Importo Originale') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotion->importo_originale) ?></dd>
            <dt scope="row"><?= __('Importo Scontato') ?></dt>
            <dd><?= $this->Number->format($kProdGasPromotion->importo_scontato) ?></dd>
            <dt scope="row"><?= __('Data Inizio') ?></dt>
            <dd><?= h($kProdGasPromotion->data_inizio) ?></dd>
            <dt scope="row"><?= __('Data Fine') ?></dt>
            <dd><?= h($kProdGasPromotion->data_fine) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kProdGasPromotion->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kProdGasPromotion->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($kProdGasPromotion->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
