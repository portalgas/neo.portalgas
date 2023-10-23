<?php 
use Cake\Core\Configure;
use App\Traits;

$user = $this->Identity->get();
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id, $user);
// debug($htmlCustomSiteOrders);
?>
<section class="content-header">
  <h1>
    <?php 
    echo (!empty($order_type_id)) ? __('Orders-'.$order_type_id): __('Orders');
    
    /* 
     * l'ordine Configure::read('Order.type.gas_groups') dev'essere creato da un ordine padre (Configure::read('Order.type.gas_parent_groups'))
     */
    if($order_type_id!=Configure::read('Order.type.gas_groups'))
      echo '<div class="pull-right">'.$this->Html->link(__('Add'), ['action' => 'add', $order_type_id], ['class'=>'btn btn-success', 'title' => __('Add Order')]).'</div>';
    else {
      // aggiungi un ordine partendo da un ordine parent (GasGroup / DES)
      echo '<div class="pull-right">'.$this->Html->link(__('Add Order to parent'), ['action' => 'add-to-parent', $order_type_id], ['class'=>'btn btn-success', 'title' => __('Add Order to parent')]).'</div>';
    }
    ?>
  </h1>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

    <?php 
      echo $htmlCustomSiteOrders->msg();
    ?>
    <?php 
    echo $this->element('search/orders');
    ?>
    <div class="box">
        <div class="box-header with-border">
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
          if(count($orders)>0) {
          ?>
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="hidden-xs hidden-sm"></th>
                  <th scope="col" colspan="2"><?= $this->Paginator->sort('supplier_organization_id', __('supplier_organization_id')) ?></th>
                  <?php 
                  /*  
                  <th scope="col"><?= $this->Paginator->sort('owner_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('owner_supplier_organization_id') ?></th>
                  */ 
                  ?>
                  <th scope="col" class="hidden-xs">Si aprirà<br />Si chiuderà</th>
                  <th scope="col" class="hidden-xs">Aperto/chiuso</th>
                  <th scope="col" class="hidden-xs"><?= $this->Paginator->sort('nota') ?></th>
                  <th scope="col" class="hidden-xs"><?= $this->Paginator->sort('owner_articles', __('OwnerArticles')) ?></th>
                  <th scope="col" class="hidden-xs"><?= $this->Paginator->sort('state_code', __('StatoElaborazione')) ?></th>
                  <th scope="col" class="hidden-xs hidden-sm"></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $colspan = 10;
              $delivery_id_old = 0;
              foreach ($orders as $order) {
           
                  if($delivery_id_old==0 || $delivery_id_old!=$order->delivery_id) {
                      echo '<tr>';
                      echo '<th class="trGroup" colspan="'.$colspan.'">';

                      echo '<a title="'.__('Visualizza il calendario della consegna').'" href="'.$this->HtmlCustomSite->jLink('deliveries', 'calendar_view', ['delivery_id' => $order->delivery->id]).'"><i class="fa fa-2x fa-calendar-check-o" aria-hidden="true"></i></a>';
                      
                      $label = $this->HtmlCustomSite->drawDeliveryLabel($order->delivery);
                      echo ' <b>'.__('Delivery').'</b> ';
                      echo '<a title="'.__('Edit Delivery').'" href="'.$this->HtmlCustomSite->jLink('deliveries', 'edit', ['delivery_id' => $order->delivery->id]).'">'.$label.'</a>';
                      echo ' '.$this->HtmlCustomSite->drawDeliveryDateLabel($order->delivery);
                      echo '</th>';
                      echo '</tr>';
                  }

                  echo '<tr>';
                  echo '<td class="hidden-xs hidden-sm">';
                  echo '<a data-toggle="collapse" data-parent="#accordion-'.$order->id.'" href="#collapse-'.$order->id.'" aria-expanded="true" title="Clicca per maggiori informazioni">';
                  echo '<i class="fa fa-2x fa-search-plus" aria-hidden="true"></i>';
                  echo '</a>';                   
                  echo '</td>';
                  echo '<td>';
                  echo $this->HtmlCustomSite->drawSupplierImage($order->suppliers_organization->supplier);
                  echo '</td>';
                  echo '<td>';
                  echo h($order->suppliers_organization->name);
                  // Order.type.gas_parent_groups e' un ordine fittizio che fa da titolare per Order.type.gas_parent_groups
                  if($order->order_type->id!=Configure::read('Order.type.gas_parent_groups'))   
                    echo '<br /><small>Importo totale '.$this->Number->format($order->tot_importo).'&nbsp;&euro;</small>';
                  echo '</td>';
                  echo '<td style="white-space:nowrap;" class="hidden-xs hidden-sm">';
                  echo $order->data_inizio->i18nFormat('eeee d MMMM');
                  echo '<br />';
                  echo $order->data_fine->i18nFormat('eeee d MMMM');
                  if($order->data_fine_validation!=Configure::read('DB.field.date.empty2'))	
                    echo '<br />Riaperto fino a '.$order->data_fine_validation->i18nFormat('eeee d MMMM');
                  echo '</td>';  
                  echo '<td style="white-space:nowrap;" class="hidden-xs">';
                  echo $this->HtmlCustomSite->drawOrderDateLabel($order);
                  echo '</td>';   
                  
                  /*
                  *  campo nota / pagamento
                  */
                  echo '<td class="hidden-xs">';
                  if(!empty($order->nota)) {
                    
                    echo '<button type="button" class="btn btn-info" data-toggle="modal" data-target="#order_nota_'.$order->id.'"><i class="fa fa-2x fa-info-circle" aria-hidden="true"></i></button>';
                    echo '<div id="order_nota_'.$order->id.'" class="modal fade" role="dialog">';
                    echo '<div class="modal-dialog">';
                    echo '<div class="modal-content">';
                    echo '<div class="modal-header">';
                    echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                    echo '<h4 class="modal-title">Nota del referente</h4>';
                    echo '</div>';
                    echo '<div class="modal-body"><p>'.$order->nota.'</p>';
                    echo '</div>';
                    echo '<div class="modal-footer">';
                    echo '<button type="button" class="btn btn-primary" data-dismiss="modal">'.__('Close').'</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';			
                    
                  } // end if(!empty($order->nota))	
                  echo '</td>';
                                    
                  echo '<td class="hidden-xs">';
                  echo __('ArticlesOwner'.$order->owner_articles);
                  echo '<br />';
                  switch ($order->order_type->id) {
                      case Configure::read('Order.type.des'):
                      case Configure::read('Order.type.des_titolare'):
                        echo '<a title="'.__('Order-'.Configure::read('Order.type.des')).'" href="'.$this->HtmlCustomSite->jLink('desOrdersOrganizations', 'index', ['des_order_id' => $order->des_order_id]).'"><small class="label bg-primary">'.$order->order_type->descri.'</small></a>';
                      break;
                      case Configure::read('Order.type.gas_parent_groups'):
                        if($user->acl['isGasGroupsManagerOrders'])
                            echo $this->Html->link('<small class="label bg-primary">'.$order->order_type->descri.'</small>', ['controller' => 'GasGroups', 'action' => 'index'], ['title' => '', 'escape' => false]);
                      break;
                      case Configure::read('Order.type.gas_groups'):
                        echo '<small class="label bg-primary">'.$order->order_type->descri.': '.$order->gas_group->name.'</small>';
                      break;
                      default:
                        echo '<small class="label bg-primary">'.$order->order_type->descri.'</small>';
                      break;
                  }
                  echo '</td>';
                  echo '<td style="white-space:nowrap;" class="hidden-xs">';
                  echo $this->HtmlCustomSite->drawOrdersStateDiv($order);
                  echo '&nbsp;';
                  echo __($order->state_code.'-label');

                 /*
                  * richiesta di pagamento 
                  */ 
                  if($user->organization->template->payToDelivery == 'POST' || $user->organization->template->payToDelivery=='ON-POST') {
                    if(!empty($order->request_payment_num)) {
                      echo "<br />";
                      if($user->acl['isTesoriere'])
                        echo '<a title="'.__('Edit RequestPayment').'" href="'.$this->HtmlCustomSite->jLink('requestPayments', 'edit', ['id' => $order->request_payment_id]).'">Rich. pagamento n. '.$order->request_payment_num.'</a>';
                      else
                        echo "<br />Rich. pagamento n. ".$order->request_payment_num;
                    }
                  }                   
                  echo '</td>';

                  /*
                  * btns / msg
                  */
                  echo '<td style="white-space: nowrap;" class="hidden-xs hidden-sm">';
                  $btns = $this->HtmlCustomSite->drawOrderBtnPaid($order, $user->acl['isRoot'], $user->acl['isTesoriere']);
                  if(!empty($btns))
                      echo $btns;	
                  else if(!empty($order->msgGgArchiveStatics))
                    echo $this->HtmlCustomSite->drawOrderMsgGgArchiveStatics($order);		
                  echo $this->HtmlCustomSite->drawOrderStateNext($order);
                  echo '</td>';
                                    
                  /* 
                   * actions 
                   */
                  echo '<td class="actions text-right">';
                  if($order->can_state_code_to_close)
                    echo '<a title="'.__('Close Order').'" class="hidden-xs" href="'.$this->HtmlCustomSite->jLink('orders', 'close', ['delivery_id' => $order->delivery_id, 'order_id' => $order->id]).'"><button type="button" class="btn btn-danger"><i class="fa fa-2x fa-power-off" aria-hidden="true"></i></button></a>';
                
                  echo '<a title="'.__('Order home').'" class="hidden-xs" href="/admin/orders/home/'.$order->order_type_id.'/'.$order->id.'"><button type="button" class="btn btn-primary"><i class="fa fa-2x fa-home" aria-hidden="true"></i></button></a>';
                  
                  if($user->acl['isRoot'] && $order->state_code=='CLOSE')
                    echo '<a title="'.__('Orders state_code change').'" href="'.$this->HtmlCustomSite->jLink('orders', 'state_code_change', ['order_id' => $order->id, 'url_bck' => 'index_history']).'" class="action action actionSyncronize"></a>';

                  // $modal_url = $this->HtmlCustomSite->jLink('orders', 'sotto_menu', ['order_id' => $order->id, 'position_img' => 'bgLeft', 'scope' => 'neo', 'format' => 'notmpl']);
                  $modal_url = '/admin/api/html-menus/order/'.$order->order_type_id.'/'.$order->id;
                  $modal_size = 'md'; // sm md lg
                  $modal_header = __('Order').' '.$order->suppliers_organization->name;                                    
                  echo '<button type="button" class="btn btn-primary btn-menu" data-attr-url="'.$modal_url.'" data-attr-size="'.$modal_size.'" data-attr-header="'.$modal_header.'"><i class="fa fa-2x fa-navicon"></i></button>';
                  // echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id), 'class'=>'btn btn-danger']);
                  
                  echo '</td>';                  
                echo '</tr>'; 
                
                echo '<tr id="collapse-'.$order->id.'" class="panel-collapse collapse" aria-expanded="true">';
                echo '<td colspan="2"></td>';
                echo '<td colspan="'.($colspan-2).'" id="collapseResult-'.$order->id.'">';
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
        </div>  <!-- /.box-body -->
      </div> <!-- /.box -->

      <?php 
      if(count($orders)>0) {
        echo $this->element('paginator');

        /*
        * legenda profilata
        */
        if(!empty($orderStatesToLegenda)) {
          echo '<span class="hidden-xs">';
          $results = $this->HtmlCustomSite->drawLegenda($user, $orderStatesToLegenda);
          echo $results['htmlLegenda'];
          $this->Html->scriptBlock($results['jsLegenda'], ['block' => true]);
          echo '</span>';
  
        }
      }
      ?>       
      
    </div>

    

  </div>
</section>

<?php 
$js = "
$(function () {";

if($order_type_id==Configure::read('Order.type.gas_parent_groups')) 
  $js .= "let ajaxUrlOrderDetail = '/admin/ajaxs/view-order-gas-parent-groups-details';";
else 
  $js .= "let ajaxUrlOrderDetail = '/admin/ajaxs/view-order-details';";
$js .= "  
  $('.btn-menu').on('click', function (e) {
    e.preventDefault();
    let url = $(this).attr('data-attr-url');
		
    let header = $(this).attr('data-attr-header');
    let size = $(this).attr('data-attr-size');
    
    let opts = new Array();
    opts = {'header': header , 'size': size};
  
    if (typeof url == 'undefined' || url == '') 
      console.error('error - url undefined!');
    else
      apriPopUpBootstrap(url, opts);
      
  });

  $('.collapse').on('show.bs.collapse', function() {
    
      let id = $(this).attr('id');
      let dataElementArray = id.split('-');
      let order_id = dataElementArray[1];
     
      let htmlResult = $('#collapseResult-'+order_id);
			htmlResult.html('');
			htmlResult.css('min-height', '50px');
			htmlResult.css('background', 'url(\"".Configure::read('App.img.cake')."/ajax-loader.gif\") no-repeat scroll center 0 transparent');
      
      let params = {
        order_id: order_id
      }

      $.ajax({url: ajaxUrlOrderDetail, 
        data: params, 
        type: 'POST',
        dataType: 'html',
        cache: false,
        headers: {
          'X-CSRF-Token': csrfToken
        },
        success: function (response) {
            console.log(response.responseText, 'responseText');
        },
        error: function (e) {
            console.error(e, ajaxUrlOrderDetail);
        },
        complete: function (e) {
            htmlResult.css('background', 'none repeat scroll 0 0 transparent');
            htmlResult.html(e.responseText);
        }
      });    
  });

  $('.collapse').on('hidden.bs.collapse', function() {
    
    let id = $(this).attr('id');
    let dataElementArray = id.split('-');
    let order_id = dataElementArray[1];

    let htmlResult = $('collapseResult-'+order_id);
    htmlResult.html('');
  }); 
});

function apriPopUpBootstrap(url, opts) {

	if (typeof url == 'undefined' || url=='') {
		console.error('error - url undefined!');
		return;
	}
		
	var modalId = 'tmpModal';
	var modalSize = 'lg';
	var modalHeader = '';
	
	if(opts!='') {
		if(opts['size']!='')
			modalSize = opts['size'];
		if(opts['header']!='')
			modalHeader = opts['header'];
	}
	
	var html = '';

	html =  '<div class=\"modal fade\" id=\"'+modalId+'\" role=\"dialog\">';
	html += '<div class=\"modal-dialog modal-'+modalSize+'\">';
	html += '<div class=\"modal-content\">';
	html += '<div class=\"modal-header\">'; 
	html += '<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>';
	html += '<h4 class=\"modal-title\">'+modalHeader+'</h4>'; // msg esito
	html += '</div>';
	html += '<div class=\"modal-body\">';
	html += '</div>';
	html += '<div class=\"modal-footer\">';
	html += '<button type=\"button\" class=\"btn btn-sm btn-warning\" data-dismiss=\"modal\">Chiudi</button>'; 
	html += '</div>'; 
	html += '</div>';
	html += '</div>';
	html += '</div>'; 
	
	$(html).appendTo('body');  /* no al body se no perdo i css */
	$('#'+modalId).modal('show');
	
	$('#'+modalId).on('shown.bs.modal', function () {
      $('#'+modalId).find('.modal-body').css('background', 'url(\"".Configure::read('App.img.cake')."/ajax-loader.gif\") no-repeat scroll center center transparent');

      $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        xhrFields: {
            withCredentials: true
        },                           
        success: function (response) {
          $('#'+modalId).find('.modal-body').css('background', 'none repeat scroll 0 0 transparent');
          $('#'+modalId).find('.modal-body').html(response);
        },
        error: function (e) {
            console.error(e, url);
        },
        complete: function (e) {
          $('#'+modalId).find('.modal-body').css('background', 'none repeat scroll 0 0 transparent');
        }
      });	
	});
	
	$('#'+modalId).on('hide.bs.modal', function () {

		$('#'+modalId).find('.modal-header').html('');            
		$('#'+modalId).find('.modal-body').html('');

		$('#'+modalId).detach();                   
	});
}
";

$this->Html->scriptBlock($js, ['block' => true]);
?>