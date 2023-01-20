<?php 
use Cake\Core\Configure;
use App\Traits;
?>
<section class="content-header">
  <h1>
    <?php 
    echo (!empty($order_type_id)) ? __('Orders-'.$order_type_id): __('Orders');
    
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
                  <th scope="col"></th>
                  <th scope="col" colspan="2"><?= $this->Paginator->sort('supplier_organization_id', __('supplier_organization_id')) ?></th>
                  <?php 
                  /*  
                  <th scope="col"><?= $this->Paginator->sort('owner_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_supplier_organization_id') ?></th>
                  */ 
                  ?>
                  <th scope="col">Si aprirà<br />Si chiuderà</th>
                  <th scope="col"><?= $this->Paginator->sort('owner_articles', __('OwnerArticles')) ?></th>
                  <th scope="col"><?= $this->Paginator->sort('state_code', __('StatoElaborazione')) ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $colspan = 9;
              $delivery_id_old = 0;
              foreach ($orders as $order) {
                
                  if($delivery_id_old==0 || $delivery_id_old!=$order->delivery_id) {
                      echo '<tr>';
                      echo '<th class="trGroup" colspan="'.$colspan.'">';
                      // echo '<b>'.__('Delivery').'</b> '.$this->Html->link($this->HtmlCustomSite->drawDeliveryLabel($order->delivery), ['controller' => 'Deliveries', 'action' => 'view', $order->delivery->id]);
                      echo '<b>'.__('Delivery').'</b> '.$this->HtmlCustomSite->drawDeliveryLabel($order->delivery);
                      echo '</th>';
                      echo '</tr>';
                  }

                  echo '<tr>';
                  echo '<td>
                      <a data-toggle="collapse" data-parent="#accordion-'.$order->id.'" href="#collapseOne-'.$order->id.'" aria-expanded="true" class="order-details">
                        <i class="fa fa-3x fa-search-plus" aria-hidden="true"></i>
                      </a>
                    </td>';
                  echo '<td>';
                  echo $this->HtmlCustomSite->drawSupplierImage($order->suppliers_organization->supplier);
                  echo '</td>';
                  echo '<td>';
                  echo h($order->suppliers_organization->name);
                  echo '<br /><small>Importo totale '.$this->Number->format($order->tot_importo).'&nbsp;&euro;</small>';
                  echo '</td>';
                  echo '<td>';
                  echo $order->data_inizio->i18nFormat('eeee d MMMM');
                  echo '<br />';
                  echo $order->data_fine->i18nFormat('eeee d MMMM');
                  if($order->data_fine_validation!=Configure::read('DB.field.date.empty2'))	
                    echo '<br />Riaperto fino a '.$order->data_fine_validation->i18nFormat('eeee d MMMM');
                  echo '</td>';                    
                  echo '<td>';
                  echo __('ArticlesOwner'.$order->owner_articles);
                  echo '<br /><small class="label bg-primary">'.$order->order_type->descri.'</small>';
                  echo '</td>';
                  echo '<td>';
                  echo $this->HtmlCustomSite->drawOrdersStateDiv($order);
                  echo '&nbsp;';
                  echo __($order->state_code.'-label');
                  echo '</td>';
                  echo '<td class="actions text-right">';
                  // echo $this->Html->link(__('View'), ['action' => 'view', $order->order_type_id, $order->id], ['class'=>'btn btn-info']);
                  echo $this->Html->link(__('Edit'), ['action' => 'edit', $order->order_type_id, $order->id], ['class'=>'btn btn-primary']);
                  echo $this->Html->link(__('ArticleOrders'), ['controller' => 'articles-orders', 'action' => 'index', $order->order_type_id, $order->id], ['class'=>'btn btn-primary']);
                  // echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class'=>'btn btn-danger']);
                  switch($order->order_type_id) {
                    case Configure::read('Order.type.gas_parent_groups'):
                        echo $this->Html->link(__('Order-'.Configure::read('Order.type.gas_groups').'-Add'), ['action' => 'add', Configure::read('Order.type.gas_groups'), $order->id], ['class'=>'btn btn-primary']);
                    break;
                    case Configure::read('Order.type.des_titolare'):
                        echo $this->Html->link(__('Order-'.Configure::read('Order.type.des').'-Add'), ['action' => 'add', Configure::read('Order.type.des'), $order->id], ['class'=>'btn btn-primary']);
                    break;
                  }
                  echo '</td>';                  
                echo '</tr>'; 
                
                echo '<tr id="collapseOne-'.$order->id.'" class="panel-collapse collapse" aria-expanded="true">';
                echo '<td colspan="2"></td>';
                echo '<td colspan="'.($colspan-2).'" id="'.$order->id.'">';
                echo "Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.";
                echo '</td>';
                echo '</tr>';

                $delivery_id_old=$order->delivery_id;
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

<?php 
$js = "
$(function () {
  $('.collapse').on('show.bs.collapse', function() {
    let ajaxUrl = 'http://portalgas.local/administrator/index.php?option=com_cake&controller=Ajax&action=view_order_details&id=33032&evidenzia=&format=notmpl';

    ajaxUrl = 'http://portalgas.local/administrator/index.php?option=com_cake&controller=ExportDocs&action=exportToReferent&delivery_id=10012&order_id=33032&doc_options=to-articles&doc_formato=PREVIEW&a=N&b=Y&c=&d=&e=&f=&g=&h=&format=notmpl';
    ajaxUrl = 'http://portalgas.local/administrator/index.php?option=com_cake&controller=Orders&action=index&format=notmpl';
    
    ajaxUrl = '/admin/bridges/index/';
    ajaxUrl = 'http://next.portalgas.it/administrator/index.php?option=com_cake&controller=Orders&action=index&format=notmpl';
   
    $.ajax({url: ajaxUrl, 
      // data: data, 
       type: 'POST',
       dataType: 'json',
       cache: false, 
       headers: {
         'X-CSRF-Token': csrfToken
       },                            
       success: function (response) {
           console.log(response);
           
           $('#33032').html(response);
       },
       error: function (e) {
           console.log(e);
       },
       complete: function (e) {
       }
   });    
  });
});
";

$this->Html->scriptBlock($js, ['block' => true]);
?>
