<section class="content-header">
  <h1>
    K Backup Orders Cart
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
            <dd><?= $kBackupOrdersCart->has('organization') ? $this->Html->link($kBackupOrdersCart->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kBackupOrdersCart->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kBackupOrdersCart->has('user') ? $this->Html->link($kBackupOrdersCart->user->name, ['controller' => 'Users', 'action' => 'view', $kBackupOrdersCart->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kBackupOrdersCart->has('order') ? $this->Html->link($kBackupOrdersCart->order->id, ['controller' => 'Orders', 'action' => 'view', $kBackupOrdersCart->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $kBackupOrdersCart->has('article') ? $this->Html->link($kBackupOrdersCart->article->name, ['controller' => 'Articles', 'action' => 'view', $kBackupOrdersCart->article->id]) : '' ?></dd>
            <dt scope="row"><?= __('DeleteToReferent') ?></dt>
            <dd><?= h($kBackupOrdersCart->deleteToReferent) ?></dd>
            <dt scope="row"><?= __('InStoreroom') ?></dt>
            <dd><?= h($kBackupOrdersCart->inStoreroom) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($kBackupOrdersCart->stato) ?></dd>
            <dt scope="row"><?= __('Article Organization Id') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersCart->article_organization_id) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersCart->qta) ?></dd>
            <dt scope="row"><?= __('Qta Forzato') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersCart->qta_forzato) ?></dd>
            <dt scope="row"><?= __('Importo Forzato') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersCart->importo_forzato) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kBackupOrdersCart->created) ?></dd>
            <dt scope="row"><?= __('Date') ?></dt>
            <dd><?= h($kBackupOrdersCart->date) ?></dd>
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
            <?= $this->Text->autoParagraph($kBackupOrdersCart->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
