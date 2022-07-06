<section class="content-header">
  <h1>
    Socialmarket Cart
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
            <dd><?= $socialmarketCart->has('organization') ? $this->Html->link($socialmarketCart->organization->name, ['controller' => 'Organizations', 'action' => 'view', $socialmarketCart->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $socialmarketCart->has('user') ? $this->Html->link($socialmarketCart->user->name, ['controller' => 'Users', 'action' => 'view', $socialmarketCart->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $socialmarketCart->has('order') ? $this->Html->link($socialmarketCart->order->id, ['controller' => 'Orders', 'action' => 'view', $socialmarketCart->order->organization_id]) : '' ?></dd>
            <dt scope="row"><?= __('Article Name') ?></dt>
            <dd><?= h($socialmarketCart->article_name) ?></dd>
            <dt scope="row"><?= __('is_active') ?></dt>
            <dd><?= h($socialmarketCart->is_active) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($socialmarketCart->id) ?></dd>
            <dt scope="row"><?= __('User Organization Id') ?></dt>
            <dd><?= $this->Number->format($socialmarketCart->user_organization_id) ?></dd>
            <dt scope="row"><?= __('Article Prezzo') ?></dt>
            <dd><?= $this->Number->format($socialmarketCart->article_prezzo) ?></dd>
            <dt scope="row"><?= __('Cart Qta') ?></dt>
            <dd><?= $this->Number->format($socialmarketCart->cart_qta) ?></dd>
            <dt scope="row"><?= __('Cart Importo Finale') ?></dt>
            <dd><?= $this->Number->format($socialmarketCart->cart_importo_finale) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($socialmarketCart->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($socialmarketCart->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($socialmarketCart->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
