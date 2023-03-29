<section class="content-header">
  <h1>
    Movement
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
            <dd><?= $movement->has('organization') ? $this->Html->link($movement->organization->name, ['controller' => 'Organizations', 'action' => 'view', $movement->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Movement Type') ?></dt>
            <dd><?= $movement->has('movement_type') ? $this->Html->link($movement->movement_type->name, ['controller' => 'MovementTypes', 'action' => 'view', $movement->movement_type->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $movement->has('user') ? $this->Html->link($movement->user->name, ['controller' => 'Users', 'action' => 'view', $movement->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($movement->name) ?></dd>
            <dt scope="row"><?= __('Type') ?></dt>
            <dd><?= h($movement->type) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($movement->id) ?></dd>
            <dt scope="row"><?= __('Supplier Organization Id') ?></dt>
            <dd><?= $this->Number->format($movement->supplier_organization_id) ?></dd>
            <dt scope="row"><?= __('Year') ?></dt>
            <dd><?= $this->Number->format($movement->year) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($movement->importo) ?></dd>
            <dt scope="row"><?= __('Date') ?></dt>
            <dd><?= h($movement->date) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($movement->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($movement->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $movement->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $movement->is_active ? __('Yes') : __('No'); ?></dd>
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
            <?= $this->Text->autoParagraph($movement->descri); ?>
        </div>
      </div>
    </div>
  </div>
</section>
