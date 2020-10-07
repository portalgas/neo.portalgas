<section class="content-header">
  <h1>
    K Organization
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
            <dd><?= h($kOrganization->name) ?></dd>
            <dt scope="row"><?= __('Indirizzo') ?></dt>
            <dd><?= h($kOrganization->indirizzo) ?></dd>
            <dt scope="row"><?= __('Localita') ?></dt>
            <dd><?= h($kOrganization->localita) ?></dd>
            <dt scope="row"><?= __('Cap') ?></dt>
            <dd><?= h($kOrganization->cap) ?></dd>
            <dt scope="row"><?= __('Provincia') ?></dt>
            <dd><?= h($kOrganization->provincia) ?></dd>
            <dt scope="row"><?= __('Telefono') ?></dt>
            <dd><?= h($kOrganization->telefono) ?></dd>
            <dt scope="row"><?= __('Telefono2') ?></dt>
            <dd><?= h($kOrganization->telefono2) ?></dd>
            <dt scope="row"><?= __('Mail') ?></dt>
            <dd><?= h($kOrganization->mail) ?></dd>
            <dt scope="row"><?= __('Www') ?></dt>
            <dd><?= h($kOrganization->www) ?></dd>
            <dt scope="row"><?= __('Www2') ?></dt>
            <dd><?= h($kOrganization->www2) ?></dd>
            <dt scope="row"><?= __('Sede Logistica 1') ?></dt>
            <dd><?= h($kOrganization->sede_logistica_1) ?></dd>
            <dt scope="row"><?= __('Sede Logistica 2') ?></dt>
            <dd><?= h($kOrganization->sede_logistica_2) ?></dd>
            <dt scope="row"><?= __('Sede Logistica 3') ?></dt>
            <dd><?= h($kOrganization->sede_logistica_3) ?></dd>
            <dt scope="row"><?= __('Sede Logistica 4') ?></dt>
            <dd><?= h($kOrganization->sede_logistica_4) ?></dd>
            <dt scope="row"><?= __('Cf') ?></dt>
            <dd><?= h($kOrganization->cf) ?></dd>
            <dt scope="row"><?= __('Piva') ?></dt>
            <dd><?= h($kOrganization->piva) ?></dd>
            <dt scope="row"><?= __('Banca') ?></dt>
            <dd><?= h($kOrganization->banca) ?></dd>
            <dt scope="row"><?= __('Banca Iban') ?></dt>
            <dd><?= h($kOrganization->banca_iban) ?></dd>
            <dt scope="row"><?= __('Lat') ?></dt>
            <dd><?= h($kOrganization->lat) ?></dd>
            <dt scope="row"><?= __('Lng') ?></dt>
            <dd><?= h($kOrganization->lng) ?></dd>
            <dt scope="row"><?= __('Img1') ?></dt>
            <dd><?= h($kOrganization->img1) ?></dd>
            <dt scope="row"><?= __('Template') ?></dt>
            <dd><?= $kOrganization->has('template') ? $this->Html->link($kOrganization->template->name, ['controller' => 'Templates', 'action' => 'view', $kOrganization->template->id]) : '' ?></dd>
            <dt scope="row"><?= __('J Seo') ?></dt>
            <dd><?= h($kOrganization->j_seo) ?></dd>
            <dt scope="row"><?= __('Gcalendar Id') ?></dt>
            <dd><?= h($kOrganization->gcalendar_id) ?></dd>
            <dt scope="row"><?= __('Type') ?></dt>
            <dd><?= h($kOrganization->type) ?></dd>
            <dt scope="row"><?= __('HasMsg') ?></dt>
            <dd><?= h($kOrganization->hasMsg) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($kOrganization->stato) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kOrganization->id) ?></dd>
            <dt scope="row"><?= __('J Group Registred') ?></dt>
            <dd><?= $this->Number->format($kOrganization->j_group_registred) ?></dd>
            <dt scope="row"><?= __('J Page Category Id') ?></dt>
            <dd><?= $this->Number->format($kOrganization->j_page_category_id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kOrganization->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kOrganization->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Descrizione') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kOrganization->descrizione); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('ParamsConfig') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kOrganization->paramsConfig); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('ParamsFields') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kOrganization->paramsFields); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('ParamsPay') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kOrganization->paramsPay); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('MsgText') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($kOrganization->msgText); ?>
        </div>
      </div>
    </div>
  </div>
</section>
