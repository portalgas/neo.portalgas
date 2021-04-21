<section class="content-header">
  <h1>
    Market Article
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
            <dd><?= $marketArticle->has('organization') ? $this->Html->link($marketArticle->organization->name, ['controller' => 'Organizations', 'action' => 'view', $marketArticle->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Market') ?></dt>
            <dd><?= $marketArticle->has('market') ? $this->Html->link($marketArticle->market->name, ['controller' => 'Markets', 'action' => 'view', $marketArticle->market->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $marketArticle->has('article') ? $this->Html->link($marketArticle->article->name, ['controller' => 'Articles', 'action' => 'view', $marketArticle->article->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($marketArticle->id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($marketArticle->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($marketArticle->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
