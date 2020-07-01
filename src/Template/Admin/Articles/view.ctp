<section class="content-header">
  <h1>
    Article
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
            <dd><?= $article->has('organization') ? $this->Html->link($article->organization->name, ['controller' => 'Organizations', 'action' => 'view', $article->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Suppliers Organization') ?></dt>
            <dd><?= $article->has('suppliers_organization') ? $this->Html->link($article->suppliers_organization->name, ['controller' => 'SuppliersOrganizations', 'action' => 'view', $article->suppliers_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Categories Article') ?></dt>
            <dd><?= $article->has('categories_article') ? $this->Html->link($article->categories_article->name, ['controller' => 'CategoriesArticles', 'action' => 'view', $article->categories_article->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($article->name) ?></dd>
            <dt scope="row"><?= __('Codice') ?></dt>
            <dd><?= h($article->codice) ?></dd>
            <dt scope="row"><?= __('Um') ?></dt>
            <dd><?= h($article->um) ?></dd>
            <dt scope="row"><?= __('Um Riferimento') ?></dt>
            <dd><?= h($article->um_riferimento) ?></dd>
            <dt scope="row"><?= __('Bio') ?></dt>
            <dd><?= h($article->bio) ?></dd>
            <dt scope="row"><?= __('Img1') ?></dt>
            <dd><?= h($article->img1) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($article->stato) ?></dd>
            <dt scope="row"><?= __('Flag Presente Articlesorders') ?></dt>
            <dd><?= h($article->flag_presente_articlesorders) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($article->id) ?></dd>
            <dt scope="row"><?= __('Prezzo') ?></dt>
            <dd><?= $this->Number->format($article->prezzo) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($article->qta) ?></dd>
            <dt scope="row"><?= __('Pezzi Confezione') ?></dt>
            <dd><?= $this->Number->format($article->pezzi_confezione) ?></dd>
            <dt scope="row"><?= __('Qta Minima') ?></dt>
            <dd><?= $this->Number->format($article->qta_minima) ?></dd>
            <dt scope="row"><?= __('Qta Massima') ?></dt>
            <dd><?= $this->Number->format($article->qta_massima) ?></dd>
            <dt scope="row"><?= __('Qta Minima Order') ?></dt>
            <dd><?= $this->Number->format($article->qta_minima_order) ?></dd>
            <dt scope="row"><?= __('Qta Massima Order') ?></dt>
            <dd><?= $this->Number->format($article->qta_massima_order) ?></dd>
            <dt scope="row"><?= __('Qta Multipli') ?></dt>
            <dd><?= $this->Number->format($article->qta_multipli) ?></dd>
            <dt scope="row"><?= __('Alert To Qta') ?></dt>
            <dd><?= $this->Number->format($article->alert_to_qta) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($article->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($article->modified) ?></dd>
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
          <h3 class="box-title"><?= __('Nota') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($article->nota); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Ingredienti') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($article->ingredienti); ?>
        </div>
      </div>
    </div>
  </div>
</section>
