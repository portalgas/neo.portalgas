<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Prod Gas Promotions

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
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('img1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_inizio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_originale') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo_scontato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contact_name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contact_mail') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('contact_phone') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kProdGasPromotions as $kProdGasPromotion): ?>
                <tr>
                  <td><?= $this->Number->format($kProdGasPromotion->id) ?></td>
                  <td><?= $kProdGasPromotion->has('organization') ? $this->Html->link($kProdGasPromotion->organization->name, ['controller' => 'Organizations', 'action' => 'view', $kProdGasPromotion->organization->id]) : '' ?></td>
                  <td><?= h($kProdGasPromotion->name) ?></td>
                  <td><?= h($kProdGasPromotion->img1) ?></td>
                  <td><?= h($kProdGasPromotion->data_inizio) ?></td>
                  <td><?= h($kProdGasPromotion->data_fine) ?></td>
                  <td><?= $this->Number->format($kProdGasPromotion->importo_originale) ?></td>
                  <td><?= $this->Number->format($kProdGasPromotion->importo_scontato) ?></td>
                  <td><?= h($kProdGasPromotion->contact_name) ?></td>
                  <td><?= h($kProdGasPromotion->contact_mail) ?></td>
                  <td><?= h($kProdGasPromotion->contact_phone) ?></td>
                  <td><?= h($kProdGasPromotion->state_code) ?></td>
                  <td><?= h($kProdGasPromotion->stato) ?></td>
                  <td><?= h($kProdGasPromotion->created) ?></td>
                  <td><?= h($kProdGasPromotion->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kProdGasPromotion->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kProdGasPromotion->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kProdGasPromotion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kProdGasPromotion->id), 'class'=>'btn btn-danger btn-xs']) ?>
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