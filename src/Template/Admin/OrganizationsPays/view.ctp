<section class="content-header">
  <h1>
    K Organizations Pay
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
            <dd><?= $organizationsPay->has('organization') ? $this->Html->link($organizationsPay->organization->name, ['controller' => 'Organizations', 'action' => 'view', $organizationsPay->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Year') ?></dt>
            <dd><?= h($organizationsPay->year) ?></dd>
            <dt scope="row"><?= __('Beneficiario Pay') ?></dt>
            <dd><?= h($organizationsPay->beneficiario_pay) ?></dd>
            <dt scope="row"><?= __('Type Pay') ?></dt>
            <dd><?= h($organizationsPay->type_pay) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->id) ?></dd>
            <dt scope="row"><?= __('Tot Users') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->tot_users) ?></dd>
            <dt scope="row"><?= __('Tot Orders') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->tot_orders) ?></dd>
            <dt scope="row"><?= __('Tot Suppliers Organizations') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->tot_suppliers_organizations) ?></dd>
            <dt scope="row"><?= __('Tot Articles') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->tot_articles) ?></dd>
            <dt scope="row"><?= __('Importo') ?></dt>
            <dd><?= $this->Number->format($organizationsPay->importo) ?></dd>
            <dt scope="row"><?= __('Data Pay') ?></dt>
            <dd><?= h($organizationsPay->data_pay) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($organizationsPay->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($organizationsPay->modified) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

</section>
