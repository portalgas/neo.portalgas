<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Mapping Types

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
              <?php foreach ($mappingTypes as $mappingType): ?>
                <tr>
                  <td><?= $this->Number->format($mappingType->id) ?></td>
                  <td><?= h($mappingType->code) ?></td>
                  <td><?= h($mappingType->name) ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTrueFalse($mappingType, 'is_system'); ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTrueFalse($mappingType, 'is_active'); ?></td>
                  <td><?= h($mappingType->is_default_ini) ?></td>
                  <td><?= h($mappingType->is_default_end) ?></td>
                  <td class="text-center"><?= $this->Number->format($mappingType->sort) ?></td>
                  <td><?= h($mappingType->created) ?></td>
                  <td><?= h($mappingType->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $mappingType->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $mappingType->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $mappingType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mappingType->id), 'class'=>'btn btn-danger btn-xs']) ?>
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