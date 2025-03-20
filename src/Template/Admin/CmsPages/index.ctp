<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CmsPage $cmsPage
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cms Page
        <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo __('Form'); ?></h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <?php echo $this->Form->create($cmsPage, ['role' => 'form']); ?>
                <div class="box-body">
                    <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('body', ['type' => 'textarea', 'class' => 'form-control wysihtml5', 'rows' => 25]);
                    ?>
                </div>
                <!-- /.box-body -->

                <?php echo $this->Form->submit(__('Submit')); ?>

                <?php echo $this->Form->end(); ?>
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>

<!-- bootstrap wysihtml5 - text editor -->
<?php echo $this->Html->css('AdminLTE./plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min', ['block' => 'css']); ?>

<!-- Bootstrap WYSIHTML5 -->
<?php echo $this->Html->script('AdminLTE./plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min', ['block' => 'script']); ?>
<?php
// echo $this->Form->control('body', ['class' => 'form-control wysihtml5']);
$js = "
$( function() {
    $('.wysihtml5').wysihtml5({
        toolbar: {
            fa: true,
            html: true,
            bold: true,
            italic: true,
            underline: true,
            link: true,
            image: true,
            lists: true,
            color: true
        },
        locale: 'it-IT'
    });
});
";
$this->Html->scriptBlock($js, ['block' => true]);
?>

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
                  <th scope="col"><?= $this->Paginator->sort('cms_menu_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cmsPages as $cmsPage): ?>
                <tr>
                    <td class="actions text-center">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $cmsPage->id], ['class'=>'btn btn-info btn-xs']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cmsPage->id], ['class'=>'btn btn-warning btn-xs']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cmsPage->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cmsPage->id), 'class'=>'btn btn-danger btn-xs']) ?>
                    </td>
                  <td><?= $cmsPage->has('cms_menu') ? $this->Html->link($cmsPage->cms_menu->name, ['controller' => 'CmsMenus', 'action' => 'view', $cmsPage->cms_menu->id]) : '' ?></td>
                  <td><?= h($cmsPage->name) ?></td>
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
