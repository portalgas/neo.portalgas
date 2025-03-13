<section class="content-header">
  <h1>
    Cms Menu
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
            <dd><?= $cmsMenu->has('organization') ? $this->Html->link($cmsMenu->organization->name, ['controller' => 'Organizations', 'action' => 'view', $cmsMenu->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Cms Menu Type') ?></dt>
            <dd><?= $cmsMenu->has('cms_menu_type') ? $this->Html->link($cmsMenu->cms_menu_type->name, ['controller' => 'CmsMenuTypes', 'action' => 'view', $cmsMenu->cms_menu_type->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($cmsMenu->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($cmsMenu->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($cmsMenu->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($cmsMenu->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($cmsMenu->modified) ?></dd>
            <dt scope="row"><?= __('Is Public') ?></dt>
            <dd><?= $cmsMenu->is_public ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $cmsMenu->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $cmsMenu->is_active ? __('Yes') : __('No'); ?></dd>
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
          <h3 class="box-title"><?= __('Options') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($cmsMenu->options); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Cms Pages') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($cmsMenu->cms_pages)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('Cms Menu Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Body') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($cmsMenu->cms_pages as $cmsPages): ?>
              <tr>
                    <td><?= h($cmsPages->id) ?></td>
                    <td><?= h($cmsPages->organization_id) ?></td>
                    <td><?= h($cmsPages->cms_menu_id) ?></td>
                    <td><?= h($cmsPages->name) ?></td>
                    <td><?= h($cmsPages->body) ?></td>
                    <td><?= h($cmsPages->created) ?></td>
                    <td><?= h($cmsPages->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'CmsPages', 'action' => 'view', $cmsPages->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'CmsPages', 'action' => 'edit', $cmsPages->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'CmsPages', 'action' => 'delete', $cmsPages->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cmsPages->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
