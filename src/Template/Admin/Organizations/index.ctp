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
              <?php foreach ($kOrganizations as $kOrganization): ?>
                <tr>
                  <td><?= $this->Number->format($kOrganization->id) ?></td>
                  <td><?= h($kOrganization->name) ?></td>
                  <td><?= h($kOrganization->indirizzo) ?></td>
                  <td><?= h($kOrganization->localita) ?></td>
                  <td><?= h($kOrganization->cap) ?></td>
                  <td><?= h($kOrganization->provincia) ?></td>
                  <td><?= h($kOrganization->telefono) ?></td>
                  <td><?= h($kOrganization->telefono2) ?></td>
                  <td><?= h($kOrganization->mail) ?></td>
                  <td><?= h($kOrganization->www) ?></td>
                  <td><?= h($kOrganization->www2) ?></td>
                  <td><?= h($kOrganization->sede_logistica_1) ?></td>
                  <td><?= h($kOrganization->sede_logistica_2) ?></td>
                  <td><?= h($kOrganization->sede_logistica_3) ?></td>
                  <td><?= h($kOrganization->sede_logistica_4) ?></td>
                  <td><?= h($kOrganization->cf) ?></td>
                  <td><?= h($kOrganization->piva) ?></td>
                  <td><?= h($kOrganization->banca) ?></td>
                  <td><?= h($kOrganization->banca_iban) ?></td>
                  <td><?= h($kOrganization->lat) ?></td>
                  <td><?= h($kOrganization->lng) ?></td>
                  <td><?= h($kOrganization->img1) ?></td>
                  <td><?= $kOrganization->has('template') ? $this->Html->link($kOrganization->template->name, ['controller' => 'Templates', 'action' => 'view', $kOrganization->template->id]) : '' ?></td>
                  <td><?= $this->Number->format($kOrganization->j_group_registred) ?></td>
                  <td><?= $this->Number->format($kOrganization->j_page_category_id) ?></td>
                  <td><?= h($kOrganization->j_seo) ?></td>
                  <td><?= h($kOrganization->gcalendar_id) ?></td>
                  <td><?= h($kOrganization->type) ?></td>
                  <td><?= h($kOrganization->hasMsg) ?></td>
                  <td><?= h($kOrganization->stato) ?></td>
                  <td><?= h($kOrganization->created) ?></td>
                  <td><?= h($kOrganization->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $kOrganization->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $kOrganization->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $kOrganization->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kOrganization->id), 'class'=>'btn btn-danger btn-xs']) ?>
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