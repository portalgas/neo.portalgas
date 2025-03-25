<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CmsPage $cmsPage
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo __('Cms Page');?>
        <small><?php echo __('List'); ?></small>
    </h1>
    <div class="pull-right"><?php echo $this->Html->link(__('Cms Add Page'), ['action' => 'add'], ['class'=>'btn btn-primary']) ?></div>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cms_menu_id', __('Cms Menu')) ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name', __('Cms Page Name')) ?></th>
                  <th scope="col" class="text-center">Totale immagini</th>
                  <th scope="col" class="text-center">Totale documenti</th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cmsPages as $cmsPage): ?>
                <tr>
                    <td class="actions text-center">
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cmsPage->id], ['class'=>'btn btn-primary']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cmsPage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cmsPage->id), 'class'=>'btn btn-danger']) ?>
                    </td>
                  <td><?= $cmsPage->has('cms_menu') ? $this->Html->link($cmsPage->cms_menu->name, ['controller' => 'CmsMenus', 'action' => 'edit', $cmsPage->cms_menu->id]) : '' ?></td>
                  <td><?= h($cmsPage->name) ?></td>
                  <td class="text-center"><?= count($cmsPage->cms_pages_images) ?></td>
                  <td class="text-center"><?= count($cmsPage->cms_pages_docs) ?></td>
                  <td><?= h($cmsPage->created) ?></td>
                  <td><?= h($cmsPage->modified) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
