<section class="content-header">
  <h1>
    Scope Mapping Type
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
            <dt scope="row"><?= __('Code') ?></dt>
            <dd><?= h($scopeMappingType->code) ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($scopeMappingType->name) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($scopeMappingType->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($scopeMappingType->sort) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($scopeMappingType->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($scopeMappingType->modified) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= $scopeMappingType->is_system ? __('Yes') : __('No'); ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $scopeMappingType->is_active ? __('Yes') : __('No'); ?></dd>
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
          <h3 class="box-title"><?= __('Descri') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($scopeMappingType->descri); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Scopes') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($scopeMappingType->scopes)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Scope Mapping Type Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Namespace') ?></th>
                    <th scope="col"><?= __('Is System') ?></th>
                    <th scope="col"><?= __('Is Active') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($scopeMappingType->scopes as $scopes): ?>
              <tr>
                    <td><?= h($scopes->id) ?></td>
                    <td><?= h($scopes->scope_mapping_type_id) ?></td>
                    <td><?= h($scopes->name) ?></td>
                    <td><?= h($scopes->namespace) ?></td>
                    <td><?= h($scopes->is_system) ?></td>
                    <td><?= h($scopes->is_active) ?></td>
                    <td><?= h($scopes->created) ?></td>
                    <td><?= h($scopes->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'Scopes', 'action' => 'view', $scopes->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'Scopes', 'action' => 'edit', $scopes->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'Scopes', 'action' => 'delete', $scopes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $scopes->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
