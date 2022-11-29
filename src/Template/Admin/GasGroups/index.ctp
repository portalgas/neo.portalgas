<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$this->start('tb_actions');
echo '<li class="sidebar-menu-action">';
echo $this->Html->link('<i class="fa fa-plus-circle"></i> <span>'.__('New').'</span>', ['action' => 'add'], ['title' => __('New'), 'escape' => false]);
echo '</li>';
$this->end();
$this->assign('tb_sidebar', $this->fetch('tb_actions')); 
?>

<section class="content-header">
  <h1>
    <?php echo __('Gas Groups');?>

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs-disabled', 'title' => __('New')]) ?></div>
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
            <?php
            /*
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
            */
            ?>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive <?php echo ($gasGroups->count()>0) ? 'no-padding': '';?>">
          <?php
          if($gasGroups->count()>0) {
          // if(!empty($gasGroups)) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class="actions text-left"><?= __('Actions') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('name') ?></th>
                <th scope="col" class="text-center"><?= __('Totate gasisti') ?></th>
                <th scope="col" class="text-center"><?= __('Totate consegne') ?></th>
                <th scope="col" class="text-center"><?= __('Totate ordini') ?></th>
                <th scope="col" class="text-center"><?= $this->Paginator->sort('is_active') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('created') ?></th>
                </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($gasGroups as $gasGroup) { 

                // debug($gasGroup);
                
                  echo '<tr>';
                  echo '<td class="actions text-left">';
                  echo $this->Html->link('', ['action' => 'edit', $gasGroup->id], ['class'=>'btn btn-primary glyphicon glyphicon-pencil', 'title' => __('Edit')]);
                  if(!$gasGroup->is_system) 
                    echo $this->Form->postLink('', ['action' => 'delete', $gasGroup->id], ['confirm' => __('Are you sure you want to delete # {0}?', $gasGroup->name), 'title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash']);
                  else
                    echo $this->Html->link('', [], ['title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash disabled']);
                  echo $this->Html->link(__('Gas Group Users Management'), ['controller' => 'GasGroupUsers', 'action' => 'management', $gasGroup->id], ['class'=>'btn btn-primary', 'title' => __('Add users')]);
                  echo '</td>';                  
                  echo '<td>'.h($gasGroup->name).'</td>';           
                  echo '<td class="text-center">'.count($gasGroup->gas_group_users).'</td>';
                  echo '<td class="text-center">'.count($gasGroup->gas_group_deliveries).'</td>';
                  echo '<td class="text-center">'.count($gasGroup->gas_group_orders).'</td>';
                  echo '<td class="text-center">'.$this->HtmlCustom->drawTrueFalse($gasGroup, 'is_active').'</td>';
                  echo '<td title="'.h($gasGroup->created).'">'.$this->Time->nice($gasGroup->created).'</td>';
                  echo '</tr>';
                } // end loop
                echo '</tbody>';
                echo '</table>';
                }
                else {
                  echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
                } // end if(!empty($gasGroups))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

      <?php echo $this->element('paginator'); ?>

    </div>
  </div>
</section>