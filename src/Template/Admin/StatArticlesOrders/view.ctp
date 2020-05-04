<section class="content-header">
  <h1>
    K Stat Articles Order
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
            <dd><?= $kStatArticlesOrder->has('organization') ? $this->Html->link($kStatArticlesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStatArticlesOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $kStatArticlesOrder->has('article') ? $this->Html->link($kStatArticlesOrder->article->name, ['controller' => 'Articles', 'action' => 'view', $kStatArticlesOrder->article->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($kStatArticlesOrder->name) ?></dd>
            <dt scope="row"><?= __('Codice') ?></dt>
            <dd><?= h($kStatArticlesOrder->codice) ?></dd>
            <dt scope="row"><?= __('Um') ?></dt>
            <dd><?= h($kStatArticlesOrder->um) ?></dd>
            <dt scope="row"><?= __('Um Riferimento') ?></dt>
            <dd><?= h($kStatArticlesOrder->um_riferimento) ?></dd>
            <dt scope="row"><?= __('Stat Order Id') ?></dt>
            <dd><?= $this->Number->format($kStatArticlesOrder->stat_order_id) ?></dd>
            <dt scope="row"><?= __('Article Organization Id') ?></dt>
            <dd><?= $this->Number->format($kStatArticlesOrder->article_organization_id) ?></dd>
            <dt scope="row"><?= __('Prezzo') ?></dt>
            <dd><?= $this->Number->format($kStatArticlesOrder->prezzo) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kStatArticlesOrder->qta) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
