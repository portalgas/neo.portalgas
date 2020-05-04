<section class="content-header">
  <h1>
    K Backup Orders Des Orders Organization
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
            <dt scope="row"><?= __('Des Order') ?></dt>
            <dd><?= $kBackupOrdersDesOrdersOrganization->has('des_order') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->des_order->id, ['controller' => 'DesOrders', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->des_order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $kBackupOrdersDesOrdersOrganization->has('organization') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $kBackupOrdersDesOrdersOrganization->has('order') ? $this->Html->link($kBackupOrdersDesOrdersOrganization->order->id, ['controller' => 'Orders', 'action' => 'view', $kBackupOrdersDesOrdersOrganization->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Luogo') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->luogo) ?></dd>
            <dt scope="row"><?= __('Contatto Nominativo') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->contatto_nominativo) ?></dd>
            <dt scope="row"><?= __('Contatto Telefono') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->contatto_telefono) ?></dd>
            <dt scope="row"><?= __('Contatto Mail') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->contatto_mail) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersDesOrdersOrganization->id) ?></dd>
            <dt scope="row"><?= __('Des Id') ?></dt>
            <dd><?= $this->Number->format($kBackupOrdersDesOrdersOrganization->des_id) ?></dd>
            <dt scope="row"><?= __('Data') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->data) ?></dd>
            <dt scope="row"><?= __('Orario') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->orario) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kBackupOrdersDesOrdersOrganization->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($kBackupOrdersDesOrdersOrganization->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
