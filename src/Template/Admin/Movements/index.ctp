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
    <?php echo __('Movements');?>

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
        <div class="box-body table-responsive <?php echo ($movements->count()>0) ? 'no-padding': '';?>">
          <?php
          if($movements->count()>0) {
          // if(!empty($movements)) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class="actions text-left"><?= __('Actions') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('organization_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('movement_type_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('supplier_organization_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('year') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('name') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('importo') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('date') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('type') ?></th>
                <th scope="col" class="text-center"><?= $this->Paginator->sort('is_system') ?></th>
                <th scope="col" class="text-center"><?= $this->Paginator->sort('is_active') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('created') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('modified') ?></th>
               </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($movements as $movement) { 

                // debug($movement);
              
  echo '<tr>';
  echo '<td class="actions text-left">';
  echo $this->Html->link('', ['action' => 'view', $movement->id], ['class'=>'btn btn-primary glyphicon glyphicon-eye-open', 'title' => __('View')]);
  echo $this->Html->link('', ['action' => 'edit', $movement->id], ['class'=>'btn btn-primary glyphicon glyphicon-pencil', 'title' => __('Edit')]);
  if(!$movement->is_system) 
    echo $this->Form->postLink('', ['action' => 'delete', $movement->id], ['confirm' => __('Are you sure you want to delete # {0}?', $movement->name), 'title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash']);
  else
    echo $this->Html->link('', [], ['title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash disabled']);
  echo '</td>';
                  echo '<td>'.$this->Number->format($movement->organization_id).'</td>';
                  echo '<td>'.$this->Number->format($movement->movement_type_id).'</td>';
                  echo '<td>'.$this->Number->format($movement->user_id).'</td>';
                  echo '<td>'.$this->Number->format($movement->supplier_organization_id).'</td>';
                  echo '<td>'.$this->Number->format($movement->year).'</td>';
                  echo '<td>'.h($movement->name).'</td>';
                  echo '<td>'.$this->Number->format($movement->importo).'</td>';
                  echo '<td>'.h($movement->date).'</td>';
                  echo '<td>';
                  echo $this->Enum->draw($movement->type, $types);
                  echo '</td>';                  
                  echo '<td class="text-center">'.$this->HtmlCustom->drawTrueFalse($movement, 'is_system', Configure::read('icon_is_system')).'</td>';
                  echo '<td class="text-center">'.$this->HtmlCustom->drawTrueFalse($movement, 'is_active').'</td>';
                  echo '<td title="'.h($movement->created).'">'.$this->Time->nice($movement->created).'</td>';
                  echo '<td title="'.h($movement->modified).'">'.$this->Time->nice($movement->modified).'</td>';
                  echo '</tr>';
              } // end loop
            echo '</tbody>';
          echo '</table>';
          }
          else {
            echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
          } // end if(!empty($movements))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

      <?php echo $this->element('paginator'); ?>

    </div>
  </div>
</section>