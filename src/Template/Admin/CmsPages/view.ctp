<section class="content-header">
  <h1>
    Cms Page
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
            <dd><?= $cmsPage->has('organization') ? $this->Html->link($cmsPage->organization->name, ['controller' => 'Organizations', 'action' => 'view', $cmsPage->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Cms Menu') ?></dt>
            <dd><?= $cmsPage->has('cms_menu') ? $this->Html->link($cmsPage->cms_menu->name, ['controller' => 'CmsMenus', 'action' => 'view', $cmsPage->cms_menu->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($cmsPage->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($cmsPage->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($cmsPage->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($cmsPage->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Body') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($cmsPage->body); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Cms Page Images') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($cmsPage->cms_page_images)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('Cms Page Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Path') ?></th>
                    <th scope="col"><?= __('Ext') ?></th>
                    <th scope="col"><?= __('Sort') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($cmsPage->cms_page_images as $cmsPageImages): ?>
              <tr>
                    <td><?= h($cmsPageImages->id) ?></td>
                    <td><?= h($cmsPageImages->organization_id) ?></td>
                    <td><?= h($cmsPageImages->cms_page_id) ?></td>
                    <td><?= h($cmsPageImages->name) ?></td>
                    <td><?= h($cmsPageImages->path) ?></td>
                    <td><?= h($cmsPageImages->ext) ?></td>
                    <td><?= h($cmsPageImages->sort) ?></td>
                    <td><?= h($cmsPageImages->created) ?></td>
                    <td><?= h($cmsPageImages->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'CmsPageImages', 'action' => 'view', $cmsPageImages->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'CmsPageImages', 'action' => 'edit', $cmsPageImages->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'CmsPageImages', 'action' => 'delete', $cmsPageImages->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cmsPageImages->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
