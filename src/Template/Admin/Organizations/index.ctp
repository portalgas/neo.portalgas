<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    K Organizations

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
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('indirizzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('localita') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cap') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('provincia') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('telefono') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('telefono2') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mail') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('www') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('www2') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sede_logistica_1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sede_logistica_2') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sede_logistica_3') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sede_logistica_4') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('cf') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('piva') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('banca') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('banca_iban') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('lat') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('lng') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('img1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('template_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('j_group_registred') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('j_page_category_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('j_seo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('gcalendar_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hasMsg') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($organizations as $organization): ?>
                <tr>
                  <td><?= $this->Number->format($organization->id) ?></td>
                  <td><?= h($organization->name) ?></td>
                  <td><?= h($organization->indirizzo) ?></td>
                  <td><?= h($organization->localita) ?></td>
                  <td><?= h($organization->cap) ?></td>
                  <td><?= h($organization->provincia) ?></td>
                  <td><?= h($organization->telefono) ?></td>
                  <td><?= h($organization->telefono2) ?></td>
                  <td><?= h($organization->mail) ?></td>
                  <td><?= h($organization->www) ?></td>
                  <td><?= h($organization->www2) ?></td>
                  <td><?= h($organization->sede_logistica_1) ?></td>
                  <td><?= h($organization->sede_logistica_2) ?></td>
                  <td><?= h($organization->sede_logistica_3) ?></td>
                  <td><?= h($organization->sede_logistica_4) ?></td>
                  <td><?= h($organization->cf) ?></td>
                  <td><?= h($organization->piva) ?></td>
                  <td><?= h($organization->banca) ?></td>
                  <td><?= h($organization->banca_iban) ?></td>
                  <td><?= h($organization->lat) ?></td>
                  <td><?= h($organization->lng) ?></td>
                  <td><?= h($organization->img1) ?></td>
                  <td><?= $organization->has('template') ? $this->Html->link($organization->template->name, ['controller' => 'Templates', 'action' => 'view', $organization->template->id]) : '' ?></td>
                  <td><?= $this->Number->format($organization->j_group_registred) ?></td>
                  <td><?= $this->Number->format($organization->j_page_category_id) ?></td>
                  <td><?= h($organization->j_seo) ?></td>
                  <td><?= h($organization->gcalendar_id) ?></td>
                  <td><?= h($organization->type) ?></td>
                  <td><?= h($organization->hasMsg) ?></td>
                  <td><?= h($organization->stato) ?></td>
                  <td><?= h($organization->created) ?></td>
                  <td><?= h($organization->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $organization->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $organization->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $organization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $organization->id), 'class'=>'btn btn-danger btn-xs']) ?>
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