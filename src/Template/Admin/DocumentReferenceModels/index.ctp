<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Document Reference Models

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
                  <th scope="col"><?= $this->Paginator->sort('code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('url_back_controller') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('url_back_action') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('url_back_params') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_system') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_default_ini') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_default_end') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($documentReferenceModels as $documentReferenceModel): ?>
                <tr>
                  <td><?= $this->Number->format($documentReferenceModel->id) ?></td>
                  <td><?= h($documentReferenceModel->code) ?></td>
                  <td><?= h($documentReferenceModel->name) ?></td>
                  <td><?= h($documentReferenceModel->url_back_controller) ?></td>
                  <td><?= h($documentReferenceModel->url_back_action) ?></td>
                  <td><?= h($documentReferenceModel->url_back_params) ?></td>
                  <td><?= h($documentReferenceModel->is_system) ?></td>
                  <td><?= h($documentReferenceModel->is_active) ?></td>
                  <td><?= h($documentReferenceModel->is_default_ini) ?></td>
                  <td><?= h($documentReferenceModel->is_default_end) ?></td>
                  <td><?= $this->Number->format($documentReferenceModel->sort) ?></td>
                  <td><?= h($documentReferenceModel->created) ?></td>
                  <td><?= h($documentReferenceModel->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $documentReferenceModel->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $documentReferenceModel->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $documentReferenceModel->id], ['confirm' => __('Are you sure you want to delete # {0}?', $documentReferenceModel->id), 'class'=>'btn btn-danger btn-xs']) ?>
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