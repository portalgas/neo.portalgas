<section class="content-header">
  <h1>
    K Stat Cart
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
            <dd><?= $kStatCart->has('organization') ? $this->Html->link($kStatCart->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatCart->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kStatCart->has('user') ? $this->Html->link($kStatCart->user->name, ['controller' => 'Users', 'action' => 'view', $kStatCart->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $kStatCart->has('article') ? $this->Html->link($kStatCart->article->name, ['controller' => 'Articles', 'action' => 'view', $kStatCart->article->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article Organization Id') ?></dt>
            <dd><?= $this->Number->format($kStatCart->article_organization_id) ?></dd>
            <dt scope="row"><?= __('Stat Order Id') ?></dt>
            <dd><?= $this->Number->format($kStatCart->stat_order_id) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kStatCart->qta) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kStatCart->importo) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
