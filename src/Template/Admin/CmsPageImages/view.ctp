<section class="content-header">
  <h1>
    Cms Page Image
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
            <dt scope="row"><?= __('Cms Page') ?></dt>
            <dd><?= $cmsPageImage->has('cms_page') ? $this->Html->link($cmsPageImage->cms_page->name, ['controller' => 'CmsPages', 'action' => 'view', $cmsPageImage->cms_page->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($cmsPageImage->name) ?></dd>
            <dt scope="row"><?= __('Ext') ?></dt>
            <dd><?= h($cmsPageImage->ext) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($cmsPageImage->id) ?></dd>
            <dt scope="row"><?= __('Organization Id') ?></dt>
            <dd><?= $this->Number->format($cmsPageImage->organization_id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($cmsPageImage->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($cmsPageImage->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($cmsPageImage->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($cmsPageImage->path); ?>
        </div>
      </div>
    </div>
  </div>
</section>
