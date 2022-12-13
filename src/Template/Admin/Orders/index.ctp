<?php 
use Cake\Core\Configure;
?>
<section class="content-header">
  <h1>
    <?php 
    echo __('Orders-'.$order_type_id);
    
    if($order_type_id==Configure::read('Order.type.gas_parent_groups') || 
       $order_type_id==Configure::read('Order.type.des_titolare')) {
        echo '<div class="pull-right">'.$this->Html->link(__('Add'), ['action' => 'add', $order_type_id], ['class'=>'btn btn-success']).'</div>';
       }          
    ?>
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
            <!-- form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div >
              </div>
            </form -->
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <?php 
          if($orders->count()>0) {
          ?>
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
                  <th scope="col" colspan="2"><?= $this->Paginator->sort('supplier_organization_id', __('supplier_organization_id')) ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_articles', __('OwnerArticles')) ?></th>
                  <?php 
                  /*  
                  <th scope="col"><?= $this->Paginator->sort('owner_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_supplier_organization_id') ?></th>
                  */ 
                  ?>
                  <th scope="col"><?= $this->Paginator->sort('delivery_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_inizio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_fine') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order) { 
                echo '<tr>';
                echo '<td class="actions text-right">';
                // echo $this->Html->link(__('View'), ['action' => 'view', $order->order_type_id, $order->id], ['class'=>'btn btn-info']);
                echo $this->Html->link(__('Edit'), ['action' => 'edit', $order->order_type_id, $order->id], ['class'=>'btn btn-primary']);
                echo $this->Html->link(__('ArticleOrders'), ['controller' => 'articles-orders', 'action' => 'index', $order->order_type_id, $order->id], ['class'=>'btn btn-primary']);
                // echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class'=>'btn btn-danger']);
                switch($order->order_type_id) {
                  case Configure::read('Order.type.gas_parent_groups'):
                      echo $this->Html->link(__('Order-'.Configure::read('Order.type.gas_groups').'-Add'), ['action' => 'add', Configure::read('Order.type.gas_groups'), $order->id], ['class'=>'btn btn-primary']);
                  break;
                  // non ancora previsto 
                  case Configure::read('Order.type.des_titolare'):
                      echo $this->Html->link(__('Order-'.Configure::read('Order.type.des').'-Add'), ['action' => 'add', Configure::read('Order.type.des'), $order->id], ['class'=>'btn btn-primary']);
                  break;
                }
                echo '</td>';
                  echo '<td>';
                  echo $this->HtmlCustomSite->drawSupplierImage($order->suppliers_organization->supplier);
                  echo '</td>';
                  echo '<td>';
                  echo h($order->suppliers_organization->name);
                  echo '</td>';
                  echo '<td>';
                  echo __('ArticlesOwner'.$order->owner_articles);
                  echo '</td>';
                // echo '<td>'.$order->has('owner_organization') ? $this->Html->link($order->owner_organization->name, ['controller' => 'OwnerOrganizations', 'action' => 'view', $order->owner_organization->id]) : ''.'</td>';
                // echo '<td>'.$order->has('owner_supplier_organization') ? $this->Html->link($order->owner_supplier_organization->name, ['controller' => 'OwnerSupplierOrganizations', 'action' => 'view', $order->owner_supplier_organization->id]) : ''.'</td>';
                  echo '<td>';
                  echo $order->has('delivery') ? $this->Html->link($order->delivery->luogo, ['controller' => 'Deliveries', 'action' => 'view', $order->delivery->id]) : '';
                  echo '</td>';                  
                  echo '<td>';
                  echo h($order->data_inizio);
                  echo '</td>';                  
                  echo '<td>';
                  echo h($order->data_fine);
                  if($order->data_fine_validation!=Configure::read('DB.field.date.empty2'))	
                    echo '<br />Riaperto fino a '.$this->Time->i18nFormat($order->data_fine_validation, "%A %e %B %Y");
                  echo '</td>';                    
                  echo '<td>';
                  echo __($order->state_code.'-label');
                  echo '</td>';                  
                  echo '<td style="text-align:center">';
                  echo $this->Number->format($order->tot_importo);
                  echo '</td>';                  
                  echo '<td>';
                  echo h($order->created);
                  echo '</td>';  
                echo '</tr>';              
              } // end loop

            echo '</tbody>';
            echo '</table>';
          }
          else {
            echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
          } // end if(!empty($orders))
          ?>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>