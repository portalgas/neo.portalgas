<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];

$this->start('tb_actions');
echo '<li class="sidebar-menu-action">';
echo $this->Html->link('<i class="fa fa-plus-circle"></i> <span>'.__('New').'</span>', ['action' => 'add'], ['title' => __('New'), 'escape' => false]);
echo '</li>';
$this->end();
$this->assign('tb_sidebar', $this->fetch('tb_actions')); 
?>

<section class="content-header">
  <h1>
    <?php echo __('Loops Orders');?>

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
        <div class="box-body table-responsive <?php echo ($loopsOrders->count()>0) ? 'no-padding': '';?>">
          <?php
          if($loopsOrders->count()>0) {
          // if(!empty($loopsOrders)) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class="actions text-left"><?= __('Actions') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('loops_delivery_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('suppliers_organization_id', __('SupplierOrganization')) ?></th>
                <th scope="col" class="text-center"><?= $this->Paginator->sort('gg_data_inizio') ?></th>
                <th scope="col" class="text-center"><?= $this->Paginator->sort('gg_data_fine') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_active') ?></th>
                <th scope="col" class=""><?= __('Created by') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('created') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class=""><?= __('Order') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($loopsOrders as $loopsOrder) { 

                // debug($loopsOrder);
              
                echo '<tr>';
                echo '<td class="actions text-left">';
                echo $this->Html->link('', ['action' => 'edit', $loopsOrder->id], ['class'=>'btn btn-primary glyphicon glyphicon-pencil', 'title' => __('Edit')]);
                echo $this->Form->postLink('', ['action' => 'delete', $loopsOrder->id], ['confirm' => __('Are you sure you want to delete # {0}?', $loopsOrder->loops_delivery->luogo), 'title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash']);
                echo '</td>';             
                echo '<td>'.h($loopsOrder->loops_delivery->luogo).'</td>';
                echo '<td>'.h($loopsOrder->suppliers_organization->name).'</td>';
                echo '<td class="text-center">'.$this->Number->format($loopsOrder->gg_data_inizio).'</td>';
                echo '<td class="text-center">'.$this->Number->format($loopsOrder->gg_data_fine).'</td>';
                echo '<td class="text-center">'.$this->HtmlCustom->drawTrueFalse($loopsOrder, 'is_active').'</td>';
                echo '<td>'.h($loopsOrder->user->username).'</td>';
                echo '<td title="'.h($loopsOrder->created).'">'.$this->Time->nice($loopsOrder->created).'</td>';
                echo '<td title="'.h($loopsOrder->modified).'">'.$this->Time->nice($loopsOrder->modified).'</td>';
                // echo '<td>'.$loopsOrder->order->delivery->luogo.'</td>';
                echo '<td>';
                if(!empty($loopsOrder->order))
                  echo '<a class="btn btn-primary" href="'.$portalgas_bo_url.'/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=home&order_id='.$loopsOrder->order->id.'">Vai all\'ordine</a>';
                echo '</td>';
                echo '</tr>';
            } // end loop
          echo '</tbody>';
          echo '</table>';
          }
          else {
            echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
          } // end if(!empty($loopsOrders))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

      <?php echo $this->element('paginator'); ?>

    </div>
  </div>
</section>