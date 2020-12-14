<section class="content-header">
  <h1>
    K Storeroom
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
            <dd><?= $kStoreroom->has('organization') ? $this->Html->link($kStoreroom->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kStoreroom->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Delivery') ?></dt>
            <dd><?= $kStoreroom->has('delivery') ? $this->Html->link($kStoreroom->delivery->id, ['controller' => 'Deliveries', 'action' => 'view', $kStoreroom->delivery->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $kStoreroom->has('user') ? $this->Html->link($kStoreroom->user->name, ['controller' => 'Users', 'action' => 'view', $kStoreroom->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Article') ?></dt>
            <dd><?= $kStoreroom->has('article') ? $this->Html->link($kStoreroom->article->name, ['controller' => 'Articles', 'action' => 'view', $kStoreroom->article->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($kStoreroom->name) ?></dd>
            <dt scope="row"><?= __('Stato') ?></dt>
            <dd><?= h($kStoreroom->stato) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kStoreroom->id) ?></dd>
            <dt scope="row"><?= __('Article Organization Id') ?></dt>
            <dd><?= $this->Number->format($kStoreroom->article_organization_id) ?></dd>
            <dt scope="row"><?= __('Qta') ?></dt>
            <dd><?= $this->Number->format($kStoreroom->qta) ?></dd>
            <dt scope="row"><?= __('Prezzo') ?></dt>
            <dd><?= $this->Number->format($kStoreroom->prezzo) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kStoreroom->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kStoreroom->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
