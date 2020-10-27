<section class="content-header">
  <h1>
    K Des Supplier
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
            <dt scope="row"><?= __('Supplier') ?></dt>
            <dd><?= $kDesSupplier->has('supplier') ? $this->Html->link($kDesSupplier->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $kDesSupplier->supplier->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($kDesSupplier->id) ?></dd>
            <dt scope="row"><?= __('Des Id') ?></dt>
            <dd><?= $this->Number->format($kDesSupplier->des_id) ?></dd>
            <dt scope="row"><?= __('Own Organization Id') ?></dt>
            <dd><?= $this->Number->format($kDesSupplier->own_organization_id) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($kDesSupplier->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($kDesSupplier->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
