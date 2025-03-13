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
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('cms_menu_id', ['options' => $cmsMenus]);
                echo $this->Form->control('name');
                echo $this->Form->control('body', ['class' => 'form-control wysihtml5']);
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
