<section class="content-header">
  <h1>
    K Prod Gas Articles Promotion
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
            <dd><?= $kProdGasArticlesPromotion->has('organization') ? $this->Html->link($kProdGasArticlesPromotion->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasArticlesPromotion->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Prod Gas Promotion') ?></dt>
            <dd><?= $kProdGasArticlesPromotion->has('prod_gas_promotion') ? $this->Html->link($kProdGasArticlesPromotion->prod_gas_promotion->name, ['controller' => 'ProdGasPromotions', 'action' => 'view', $kProdGasArticlesPromotion->prod_gas_promotion->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $kProdGasArticlesPromotion->has('article') ? $this->Html->link($kProdGasArticlesPromotion->article->name, ['controller' => 'Articles', 'action' => 'view', $kProdGasArticlesPromotion->article->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kProdGasArticlesPromotion->id) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kProdGasArticlesPromotion->qta) ?></dd>
            <dt scope="row"><?= __('Prezzo Unita') ?></dt>
            <dd><?= $this->Number->format($kProdGasArticlesPromotion->prezzo_unita) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($kProdGasArticlesPromotion->importo) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kProdGasArticlesPromotion->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kProdGasArticlesPromotion->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
