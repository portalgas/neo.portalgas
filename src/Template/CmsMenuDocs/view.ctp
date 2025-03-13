<section class="content-header">
  <h1>
    Cms Menu Doc
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
            <dd><?= $cmsMenuDoc->has('organization') ? $this->Html->link($cmsMenuDoc->organization->name, ['controller' => 'Organizations', 'action' => 'view', $cmsMenuDoc->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Cms Menu') ?></dt>
            <dd><?= $cmsMenuDoc->has('cms_menu') ? $this->Html->link($cmsMenuDoc->cms_menu->name, ['controller' => 'CmsMenus', 'action' => 'view', $cmsMenuDoc->cms_menu->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($cmsMenuDoc->name) ?></dd>
            <dt scope="row"><?= __('Ext') ?></dt>
            <dd><?= h($cmsMenuDoc->ext) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($cmsMenuDoc->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($cmsMenuDoc->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($cmsMenuDoc->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Path') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($cmsMenuDoc->path); ?>
        </div>
      </div>
    </div>
  </div>
</section>
