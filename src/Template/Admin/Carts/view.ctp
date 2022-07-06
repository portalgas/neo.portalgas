<section class="content-header">
  <h1>
    K Cart
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
            <dt scope="row"><?= __('DeleteToReferent') ?></dt>
            <dd><?= h($kCart->deleteToReferent) ?></dd>
            <dt scope="row"><?= __('InStoreroom') ?></dt>
            <dd><?= h($kCart->inStoreroom) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($kCart->stato) ?></dd>
            <dt scope="row"><?= __('Organization Id') ?></dt>
            <dd><?= $this->Number->format($kCart->organization_id) ?></dd>
            <dt scope="row"><?= __('User Id') ?></dt>
            <dd><?= $this->Number->format($kCart->user_id) ?></dd>
            <dt scope="row"><?= __('Order Id') ?></dt>
            <dd><?= $this->Number->format($kCart->order_id) ?></dd>
            <dt scope="row"><?= __('Article Organization Id') ?></dt>
            <dd><?= $this->Number->format($kCart->article_organization_id) ?></dd>
            <dt scope="row"><?= __('Article Id') ?></dt>
            <dd><?= $this->Number->format($kCart->article_id) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kCart->qta) ?></dd>
            <dt scope="row"><?= __('Qta Forzato') ?></dt>
            <dd><?= $this->Number->format($kCart->qta_forzato) ?></dd>
            <dt scope="row"><?= __('Importo Forzato') ?></dt>
            <dd><?= $this->Number->format($kCart->importo_forzato) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kCart->created) ?></dd>
            <dt scope="row"><?= __('Date') ?></dt>
            <dd><?= h($kCart->date) ?></dd>
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
            <?= $this->Text->autoParagraph($kCart->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
