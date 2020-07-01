<section class="content-header">
  <h1>
    Articles Order
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
            <dd><?= $articlesOrder->has('organization') ? $this->Html->link($articlesOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $articlesOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $articlesOrder->has('order') ? $this->Html->link($articlesOrder->order->id, ['controller' => 'Orders', 'action' => 'view', $articlesOrder->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article Organization') ?></dt>
            <dd><?= $articlesOrder->has('article_organization') ? $this->Html->link($articlesOrder->article_organization->name, ['controller' => 'Organizations', 'action' => 'view', $articlesOrder->article_organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $articlesOrder->has('article') ? $this->Html->link($articlesOrder->article->name, ['controller' => 'Articles', 'action' => 'view', $articlesOrder->article->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($articlesOrder->name) ?></dd>
            <dt scope="row"><?= __('Send Mail') ?></dt>
            <dd><?= h($articlesOrder->send_mail) ?></dd>
            <dt scope="row"><?= __('Flag Bookmarks') ?></dt>
            <dd><?= h($articlesOrder->flag_bookmarks) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($articlesOrder->stato) ?></dd>
            <dt scope="row"><?= __('Qta Cart') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_cart) ?></dd>
            <dt scope="row"><?= __('Prezzo') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->prezzo) ?></dd>
            <dt scope="row"><?= __('Pezzi Confezione') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->pezzi_confezione) ?></dd>
            <dt scope="row"><?= __('Qta Minima') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_minima) ?></dd>
            <dt scope="row"><?= __('Qta Massima') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_massima) ?></dd>
            <dt scope="row"><?= __('Qta Minima Order') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_minima_order) ?></dd>
            <dt scope="row"><?= __('Qta Massima Order') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_massima_order) ?></dd>
            <dt scope="row"><?= __('Qta Multipli') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->qta_multipli) ?></dd>
            <dt scope="row"><?= __('Alert To Qta') ?></dt>
            <dd><?= $this->Number->format($articlesOrder->alert_to_qta) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($articlesOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($articlesOrder->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
