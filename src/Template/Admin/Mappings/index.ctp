<?php

echo '<section class="content-header">';
echo '<h1>'.__('Mappings');

echo '<div class="btn-group pull-right" role="group" aria-label="news">';
foreach($btns_queues as $btns_queue) {
    $label = __('New').' '.$btns_queue->name;
    echo $this->Html->link($label, ['action' => 'add', $btns_queue->id, 
                                  '?' => [
                                          'search_queue_id' => $search_queue_id,
                                          'search_master_scope_id' => $search_master_scope_id,
                                          'search_master_table_id' => $search_master_table_id,
                                          'search_mapping_type_id' => $search_mapping_type_id,
                                          'search_slave_scope_id' => $search_slave_scope_id,
                                          'search_slave_table_id' => $search_slave_table_id 
                                          ]], 
                                          ['class'=>'btn btn-success']);    
}
echo '</div>';
echo '</h1>';
echo '</section>';
?>


<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <?php
        echo $this->element('mappings_search', ['totResults' => $mappings->count(), 'queues' => $queues, 'master_scopes' => $master_scopes, 'slave_scopes' => $slave_scopes, 'master_tables' => $master_tables, 'slave_tables' => $slave_tables, 'mapping_types' => $mapping_types
        ]);
        ?>
    </div>
  </div>

  <?php
  if(empty($mappings) || $mappings->count()==0) {
      echo '<div class="row">';
      echo '<div class="col-xs-12">';
      echo '<div class="box">';
      echo $this->element('msgResults');
      echo '</div>';
      echo '</div>';
      echo '</div>';
  }    
  else {
  ?>
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
                  <th scope="col"><?= $this->Paginator->sort('queue_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_table_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('master_column') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_scope_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_table_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('slave_column') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('mapping_type') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('value') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('sort') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_required') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mappings as $mapping): ?>
                <tr>
                  <td><?= $this->Number->format($mapping->id) ?></td>
                  <td><?= h($mapping->queue->name) ?></td>
                  <td><?= h($mapping->name) ?></td>
                  <td>
                  <?php
                    echo ($mapping->has('master_scope')) ? h($mapping->master_scope->name) : '';
                  ?>
                  </td>
                  <td>
                  <?php
                    if($mapping->has('master_table')) {
                        echo h($mapping->master_table->name);
                        if($mapping->master_table->scope_id!=$mapping->master_scope->id)
                            echo '<br /><span class="label label-danger">dato inconsitente: controllare masterScope</span>';                        
                    }                  
                  ?>
                  </td>
                  <td><?= h($mapping->master_column) ?></td>
                  <td><?= h($mapping->slave_scope->name) ?></td>
                  <td><?= h($mapping->slave_table->name);
                      if($mapping->slave_table->scope_id!=$mapping->slave_scope->id)
                          echo '<br /><span class="label label-danger">dato inconsitente: controllare slaveScope</span>';
                      ?>
                  </td>
                  <td><?= h($mapping->slave_column) ?></td>
                  <td><?= $mapping->has('mapping_type') ? $this->Html->link($mapping->mapping_type->name, ['controller' => 'MappingTypes', 'action' => 'view', $mapping->mapping_type->id]) : '' ?></td>
                  <td>
                    <?php
                    echo $this->HtmlCustomSite->translateMappingTypeCode($mapping);
                    ?>
                  </td>
                  <td class="text-center"><?= $this->Number->format($mapping->sort) ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($mapping, $mapping->is_required); ?></td>
                  <td class="text-center"><?= $this->HtmlCustom->drawTruFalse($mapping, $mapping->is_active); ?></td>
                  <td class="actions text-right">
                      <?php
                      echo $this->Html->link(__('Edit'), ['action' => 'edit', $mapping->id, '?' => [
                        'search_queue_id' => $search_queue_id,
                        'search_master_scope_id' => $search_master_scope_id,
                        'search_master_table_id' => $search_master_table_id,
                        'search_mapping_type_id' => $search_mapping_type_id,
                        'search_slave_scope_id' => $search_slave_scope_id,
                        'search_slave_table_id' => $search_slave_table_id 
                        ]], ['class'=>'btn btn-warning btn-xs']);
                    
                      echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $mapping->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mapping->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
  
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>

    </div>
  </div>
  <?php
  }
  ?>

</section>
