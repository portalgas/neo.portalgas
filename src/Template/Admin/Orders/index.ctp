<?php 
use Cake\Core\Configure;
use App\Traits;

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$portalgas_fe_url = $config['Portalgas.fe.url']; 
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
                      <a data-toggle="collapse" data-parent="#accordion-'.$order->id.'" href="#collapse-'.$order->id.'" aria-expanded="true" title="Clicca per maggiori informazioni">
                        <i class="fa fa-2x fa-search-plus" aria-hidden="true"></i>
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

                  echo '<button type="button" class="btn btn-primary btn-menu" data-attr-url="'.$portalgas_bo_url.'/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=sotto_menu&amp;order_id='.$order->id.'&amp;position_img=bgLeft&amp;scope=neo&amp;format=notmpl" data-attr-size="md" data-attr-header="Ordine Azienda agricola RoeroVero"><i class="fa fa-2x fa-navicon"></i></button>';
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
			htmlResult.css('background', 'url(\"/images/cake/ajax-loader.gif\") no-repeat scroll center 0 transparent');
      
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
      $('#'+modalId).find('.modal-body').css('background', 'url(\"/images/cake/ajax-loader.gif\") no-repeat scroll center center transparent');

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
            console.error(e, ajaxUrl);
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