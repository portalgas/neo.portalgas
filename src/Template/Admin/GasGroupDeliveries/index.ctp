<?php
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\I18n\FrozenTime;

$this->start('tb_actions');
echo '<li class="sidebar-menu-action">';
echo $this->Html->link('<i class="fa fa-plus-circle"></i> <span>'.__('New').'</span>', ['action' => 'add'], ['title' => __('New'), 'escape' => false]);
echo '</li>';
$this->end();
$this->assign('tb_sidebar', $this->fetch('tb_actions')); 
?>

<section class="content-header">
  <h1>
    <?php echo __('Deliveries');?>

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
        <div class="box-body table-responsive <?php echo ($gasGroupDeliveries->count()>0) ? 'no-padding': '';?>">
          <?php
          if($gasGroupDeliveries->count()>0) {
          // if(!empty($gasGroupDeliveries)) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
            <th scope="col" class="actions text-left"><?= __('Actions') ?></th>
            <th scope="col" class=""><?= $this->Paginator->sort('Gas Group') ?></th>
            <th scope="col" class="text-center"><?= __('Tot gasisti') ?></th>
            <th scope="col" class=""><?= $this->Paginator->sort('luogo') ?></th>
            <th scope="col" class=""><?= $this->Paginator->sort('data') ?></th>
            <th scope="col" class=""><?= __('Aperto/chiuso') ?></th>
            <th scope="col" class="text-center"><?= __('Tot Ordini') ?></th>
            <th scope="col" class=""><?= $this->Paginator->sort('created') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($gasGroupDeliveries as $gasGroupDelivery) { 
                
                // debug($gasGroupDelivery);
                
                $delivery = $gasGroupDelivery->delivery;
                $now = FrozenTime::parse('now');
                $interval = $now->diff($delivery->data);
                $delta = $interval->format('%R');
                if($delta==='-')
                  $diff_label = 'Passati ';
                else
                  $diff_label = 'Mancano ';
                $diff = $interval->format('%a giorni');
                $diff_label = $diff_label.$diff;

                // debug($delivery);
                                
                  echo '<tr>';
                  echo '<td class="actions text-left">';
                  // echo $this->Html->link('', ['action' => 'view', $delivery->id], ['class'=>'btn btn-primary glyphicon glyphicon-eye-open', 'title' => __('View')]);
                  echo $this->Html->link('', ['action' => 'edit', $delivery->id], ['class'=>'btn btn-primary glyphicon glyphicon-pencil', 'title' => __('Edit')]);
                  if(!$delivery->is_system) 
                    echo $this->Form->postLink('', ['action' => 'delete', $delivery->id], ['confirm' => __('Are you sure you want to delete # {0}?', $delivery->name), 'title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash']);
                  else
                    echo $this->Html->link('', [], ['title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash disabled']);
                  echo '</td>';             
                  echo '<td>'.h($gasGroupDelivery->gas_group->name).'</td>';
                  echo '<td class="text-center">'.count($gasGroupDelivery->gas_group->gas_group_users).'</td>';
                  echo '<td>'.h($delivery->luogo).'</td>';
                  echo '<td>';
                  if($delivery->sys=='N')
                    echo $delivery->data;
                    // echo $this->Time->nice($delivery->data);
                  echo '</td>';
                  echo '<td>';
                  if($delivery->sys=='N')
                    echo $diff_label;
                  echo '</td>';
                  echo '<td class="text-center">'.count($delivery->orders).'</td>';
                  if($delivery->sys=='N')
                    echo '<td title="'.h($delivery->created).'">'.$delivery->created.'</td>';
                  else 
                    echo '<td></td>';
                  echo '</tr>';
              } // end loop
            echo '</tbody>';
          echo '</table>';
          }
          else {
            echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
          } // end if(!empty($gasGroupDeliveries))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

      <?php echo $this->element('paginator'); ?>

    </div>
  </div>
</section>