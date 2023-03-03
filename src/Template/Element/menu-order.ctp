<?php 
use Cake\Core\Configure;

/*
 * visualizza il menu profilato dell'ordine 
*/
if(isset($order) && !empty($order->id)) {

  $js = "$(function () { 
  let ajaxUrl".$order->id." = '/admin/api/html-menus/order/".$order->order_type_id."/".$order->id."/menu';
  let htmlResult".$order->id." = $('#box-menu-order');
  htmlResult".$order->id.".css('background', 'url(\"".Configure::read('App.img.cake')."/ajax-loader.gif\") no-repeat scroll center center transparent');
  $.ajax({url: ajaxUrl".$order->id.", 
    type: 'GET',
    dataType: 'html',
    cache: false,
    headers: {
      'X-CSRF-Token': csrfToken
    },
    success: function (response) {
        htmlResult".$order->id.".css('background', 'none repeat scroll 0 0 transparent');
        htmlResult".$order->id.".html(response);

        /* apro il menu' */
        var a = $('#box-menu-order a');
        a.parent().addClass('active').parents('.treeview').addClass('active');

    },
    error: function (e) {
        console.error(e, ajaxUrl".$order->id.");
    },
    complete: function (e) {
       htmlResult".$order->id.".css('background', 'none repeat scroll 0 0 transparent');
       htmlResult".$order->id.".html(e.responseText);
    }
  });  
  
  });";

  $this->Html->scriptBlock($js, ['block' => true]);
}
