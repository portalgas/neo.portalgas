<section class="content-header">
  <h1>
    K Suppliers Vote
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
            <dd><?= $suppliersVote->has('supplier') ? $this->Html->link($suppliersVote->supplier->name, ['controller' => 'Suppliers', 'action' => 'view', $suppliersVote->supplier->id]) : '' ?></dd>
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $suppliersVote->has('organization') ? $this->Html->link($suppliersVote->organization->name, ['controller' => 'Organizations', 'action' => 'view', $suppliersVote->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('User') ?></dt>
            <dd><?= $suppliersVote->has('user') ? $this->Html->link($suppliersVote->user->name, ['controller' => 'Users', 'action' => 'view', $suppliersVote->user->id]) : '' ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($suppliersVote->id) ?></dd>
            <dt scope="row"><?= __('Voto') ?></dt>
            <dd><?= $this->Number->format($suppliersVote->voto) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($suppliersVote->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($suppliersVote->modified) ?></dd>
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
            <?= $this->Text->autoParagraph($suppliersVote->nota); ?>
        </div>
      </div>
    </div>
  </div>
</section>
