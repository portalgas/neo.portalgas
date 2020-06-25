<section class="content-header">
  <h1>
    Price Type
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
            <dd><?= $priceType->has('organization') ? $this->Html->link($priceType->organization->name, ['controller' => 'Organizations', 'action' => 'view', $priceType->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Order') ?></dt>
            <dd><?= $priceType->has('order') ? $this->Html->link($priceType->order->id, ['controller' => 'Orders', 'action' => 'view', $priceType->order->id]) : '' ?></dd>
            <dt scope="row"><?= __('Code') ?></dt>
            <dd><?= h($priceType->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($priceType->name) ?></dd>
            <dt scope="row"><?= __('Type') ?></dt>
            <dd><?= h($priceType->type) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($priceType->id) ?></dd>
            <dt scope="row"><?= __('Value') ?></dt>
            <dd><?= $this->Number->format($priceType->value) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($priceType->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($priceType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($priceType->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $priceType->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $priceType->is_active ? __('Yes') : __('No'); ?></dd>
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
          <h3 class="box-title"><?= __('Descri') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($priceType->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
