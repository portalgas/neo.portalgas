<section class="content-header">
  <h1>
    Loops Order
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
            <dd><?= $loopsOrder->has('organization') ? $this->Html->link($loopsOrder->organization->name, ['controller' => 'Organizations', 'action' => 'view', $loopsOrder->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $loopsOrder->has('user') ? $this->Html->link($loopsOrder->user->name, ['controller' => 'Users', 'action' => 'view', $loopsOrder->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Flag Send Mail') ?></dt>
            <dd><?= h($loopsOrder->flag_send_mail) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($loopsOrder->id) ?></dd>
            <dt scope="row"><?= __('Loops Delivery Id') ?></dt>
            <dd><?= $this->Number->format($loopsOrder->loops_delivery_id) ?></dd>
            <dt scope="row"><?= __('Supplier Organization Id') ?></dt>
            <dd><?= $this->Number->format($loopsOrder->supplier_organization_id) ?></dd>
            <dt scope="row"><?= __('Gg Data Inizio') ?></dt>
            <dd><?= $this->Number->format($loopsOrder->gg_data_inizio) ?></dd>
            <dt scope="row"><?= __('Gg Data Fine') ?></dt>
            <dd><?= $this->Number->format($loopsOrder->gg_data_fine) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($loopsOrder->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($loopsOrder->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
