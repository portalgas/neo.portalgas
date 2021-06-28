<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\JContent $jContent
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      J Content
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
          <?php echo $this->Form->create($jContent, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('asset_id');
                echo $this->Form->control('title');
                echo $this->Form->control('alias');
                echo $this->Form->control('title_alias');
                echo $this->Form->control('introtext');
                echo $this->Form->control('fulltext');
                echo $this->Form->control('state');
                echo $this->Form->control('sectionid');
                echo $this->Form->control('mask');
                echo $this->Form->control('catid');
                echo $this->Form->control('created_by');
                echo $this->Form->control('created_by_alias');
                echo $this->Form->control('modified_by');
                echo $this->Form->control('checked_out');
                echo $this->Form->control('checked_out_time');
                echo $this->Form->control('publish_up');
                echo $this->Form->control('publish_down');
                echo $this->Form->control('images');
                echo $this->Form->control('urls');
                echo $this->Form->control('attribs');
                echo $this->Form->control('version');
                echo $this->Form->control('parentid');
                echo $this->Form->control('ordering');
                echo $this->Form->control('metakey');
                echo $this->Form->control('metadesc');
                echo $this->Form->control('access');
                echo $this->Form->control('hits');
                echo $this->Form->control('metadata');
                echo $this->Form->control('featured');
                echo $this->Form->control('language');
                echo $this->Form->control('xreference');
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
