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
    echo __('Add Order to parent');
    ?>
  </h1>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
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
                  <th scope="col" class="hidden-xs"><?= $this->Paginator->sort('state_code', __('StatoElaborazione')) ?></th>
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
                  if($order->order_type->id!=Configure::read('Order.type.gas_parent_groups')) {
                    echo '<a data-toggle="collapse" data-parent="#accordion-'.$order->id.'" href="#collapse-'.$order->id.'" aria-expanded="true" title="Clicca per maggiori informazioni">';
                    echo '<i class="fa fa-2x fa-search-plus" aria-hidden="true"></i>';
                    echo '</a>';
                  }
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
                  echo '<td style="white-space:nowrap;" class="hidden-xs">';
                  echo $this->HtmlCustomSite->drawOrdersStateDiv($order);
                  echo '&nbsp;';
                  echo __($order->state_code.'-label');
                  echo '</td>';

                  /* 
                   * actions 
                   */
                  echo '<td class="actions text-right">';
                  if(empty($order->gas_groups_childs))
                    echo $this->Html->link(__('Add OrderGasGroup'), ['action' => 'add', Configure::read('Order.type.gas_groups'), $order->id], ['class'=>'btn btn-primary', 'title' => __('Add OrderGasGroup')]);
                  else 
                    echo $this->Html->link(__('OrderGasGroup Home'), ['action' => 'home', Configure::read('Order.type.gas_groups'), $order->gas_groups_childs[0]->id], ['class'=>'btn btn-primary', 'title' => __('OrderGasGroup Home')]);
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
        echo '<span class="hidden-xs">';
        $results = $this->HtmlCustomSite->drawLegenda($user, $orderStatesToLegenda);
        echo $results['htmlLegenda'];
        $this->Html->scriptBlock($results['jsLegenda'], ['block' => true]);
        echo '</span>';
      }
      ?>       
      
    </div>

    

  </div>
</section>

<?php 
$js = "
$(function () {
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

      let ajaxUrl = '/admin/ajaxs/view-order-details';
      
      let htmlResult = $('#collapseResult-'+order_id);
			htmlResult.html('');
			htmlResult.css('min-height', '50px');
			htmlResult.css('background', 'url(\"".Configure::read('App.img.cake')."/ajax-loader.gif\") no-repeat scroll center 0 transparent');
      
      let params = {
        order_id: order_id
      }

      $.ajax({url: ajaxUrl, 
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
            console.error(e, ajaxUrl);
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