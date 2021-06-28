<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    J Content

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
                  <th scope="col"><?= $this->Paginator->sort('asset_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('alias') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('title_alias') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sectionid') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mask') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('catid') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created_by') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created_by_alias') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified_by') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('checked_out') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('checked_out_time') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('publish_up') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('publish_down') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('attribs') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('version') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('parentid') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('ordering') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('access') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('hits') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('featured') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('language') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('xreference') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($jContent as $jContent): ?>
                <tr>
                  <td><?= $this->Number->format($jContent->id) ?></td>
                  <td><?= $this->Number->format($jContent->asset_id) ?></td>
                  <td><?= h($jContent->title) ?></td>
                  <td><?= h($jContent->alias) ?></td>
                  <td><?= h($jContent->title_alias) ?></td>
                  <td><?= $this->Number->format($jContent->state) ?></td>
                  <td><?= $this->Number->format($jContent->sectionid) ?></td>
                  <td><?= $this->Number->format($jContent->mask) ?></td>
                  <td><?= $this->Number->format($jContent->catid) ?></td>
                  <td><?= h($jContent->created) ?></td>
                  <td><?= $this->Number->format($jContent->created_by) ?></td>
                  <td><?= h($jContent->created_by_alias) ?></td>
                  <td><?= h($jContent->modified) ?></td>
                  <td><?= $this->Number->format($jContent->modified_by) ?></td>
                  <td><?= $this->Number->format($jContent->checked_out) ?></td>
                  <td><?= h($jContent->checked_out_time) ?></td>
                  <td><?= h($jContent->publish_up) ?></td>
                  <td><?= h($jContent->publish_down) ?></td>
                  <td><?= h($jContent->attribs) ?></td>
                  <td><?= $this->Number->format($jContent->version) ?></td>
                  <td><?= $this->Number->format($jContent->parentid) ?></td>
                  <td><?= $this->Number->format($jContent->ordering) ?></td>
                  <td><?= $this->Number->format($jContent->access) ?></td>
                  <td><?= $this->Number->format($jContent->hits) ?></td>
                  <td><?= $this->Number->format($jContent->featured) ?></td>
                  <td><?= h($jContent->language) ?></td>
                  <td><?= h($jContent->xreference) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $jContent->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $jContent->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $jContent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $jContent->id), 'class'=>'btn btn-danger btn-xs']) ?>
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