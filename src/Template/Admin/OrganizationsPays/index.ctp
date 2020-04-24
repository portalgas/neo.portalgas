<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Organizations Pays

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
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('year') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('beneficiario_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_users') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_orders') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_suppliers_organizations') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_articles') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('type_pay') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($organizationsPays as $organizationsPay): ?>
                <tr>
                  <td><?= $organizationsPay->has('organization') ? $this->Html->link($organizationsPay->organization->name, ['controller' => 'Organizations', 'action' => 'view', $organizationsPay->organization->id]) : '' ?></td>
                  <td><?= h($organizationsPay->year) ?></td>
                  <td><?= h($organizationsPay->data_pay) ?></td>
                  <td><?= h($organizationsPay->beneficiario_pay) ?></td>
                  <td><?= $this->Number->format($organizationsPay->tot_users) ?></td>
                  <td><?= $this->Number->format($organizationsPay->tot_orders) ?></td>
                  <td><?= $this->Number->format($organizationsPay->tot_suppliers_organizations) ?></td>
                  <td><?= $this->Number->format($organizationsPay->tot_articles) ?></td>
                  <td><?= $this->Number->format($organizationsPay->importo) ?></td>
                  <td><?= h($organizationsPay->type_pay) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $organizationsPay->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $organizationsPay->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $organizationsPay->id], ['confirm' => __('Are you sure you want to delete # {0}?', $organizationsPay->id), 'class'=>'btn btn-danger btn-xs']) ?>
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