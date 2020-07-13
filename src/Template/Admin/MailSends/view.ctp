<section class="content-header">
  <h1>
    Mail Send
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
            <dd><?= $mailSend->has('organization') ? $this->Html->link($mailSend->organization->name, ['controller' => 'Organizations', 'action' => 'view', $mailSend->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('File Sh') ?></dt>
            <dd><?= h($mailSend->file_sh) ?></dd>
            <dt scope="row"><?= __('Cron') ?></dt>
            <dd><?= h($mailSend->cron) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($mailSend->id) ?></dd>
            <dt scope="row"><?= __('Tot Users') ?></dt>
            <dd><?= $this->Number->format($mailSend->tot_users) ?></dd>
            <dt scope="row"><?= __('Data') ?></dt>
            <dd><?= h($mailSend->data) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($mailSend->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($mailSend->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
