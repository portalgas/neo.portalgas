<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Supplier Organization Cash Excludeds

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs']) ?></div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kSupplierOrganizationCashExcludeds as $kSupplierOrganizationCashExcluded): ?>
                <tr>
                  <td><?= $this->Number->format($kSupplierOrganizationCashExcluded->id) ?></td>
                  <td><?= $kSupplierOrganizationCashExcluded->has('organization') ? $this->Html->link($kSupplierOrganizationCashExcluded->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kSupplierOrganizationCashExcluded->organization->id]) : '' ?></td>
                  <td><?= $this->Number->format($kSupplierOrganizationCashExcluded->supplier_organization_id) ?></td>
                  <td><?= h($kSupplierOrganizationCashExcluded->created) ?></td>
                  <td><?= h($kSupplierOrganizationCashExcluded->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kSupplierOrganizationCashExcluded->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kSupplierOrganizationCashExcluded->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kSupplierOrganizationCashExcluded->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kSupplierOrganizationCashExcluded->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>