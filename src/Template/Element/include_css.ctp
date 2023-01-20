<?php 
use Cake\Core\Configure; 

// Bootstrap 3.3.7 
echo $this->Html->css('AdminLTE./bower_components/bootstrap/dist/css/bootstrap.min', ['block' => 'css']); 

// Font Awesome 4.7.0
echo $this->Html->css('AdminLTE./bower_components/font-awesome/css/font-awesome.min', ['block' => 'css']); 

// Ionicons 
echo $this->Html->css('AdminLTE./bower_components/Ionicons/css/ionicons.min', ['block' => 'css']); 

// Theme style 
echo $this->Html->css('AdminLTE.AdminLTE.min', ['block' => 'css']); 
// AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. 
echo $this->Html->css('AdminLTE.skins/skin-'. Configure::read('Theme.skin') .'.min', ['block' => 'css']); 

// Select2 
echo $this->Html->css('AdminLTE./bower_components/select2/dist/css/select2.min', ['block' => 'css']); 

// bootstrap datepicker 
echo $this->Html->css('AdminLTE./bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min', ['block' => 'css']); 

// bootstrap-daterangepicker
echo $this->Html->css('AdminLTE./bower_components/bootstrap-daterangepicker/daterangepicker', ['block' => 'css']);

// iCheck for checkboxes and radio inputs 
echo $this->Html->css('AdminLTE./plugins/iCheck/all', ['block' => 'css']); 

// DataTables 
echo $this->Html->css('AdminLTE./bower_components/datatables.net-bs/css/dataTables.bootstrap.min', ['block' => 'css']); 

echo $this->Html->css('style'); 
echo $this->Html->css('my.min.css?v=1.0');

// HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries 
// WARNING: Respond.js doesn't work if you view the page via file:// 
//[if lt IE 9]>
echo '<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>';
echo '<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>';
echo '<![endif]';
echo '// Google Font ';
echo '<link href="https://fonts.googleapis.com/css?family=Raleway:500i|Roboto:300,400,700|Roboto+Mono" rel="stylesheet">';
?>
<style>
.datepicker {
  z-index: 1040 !important; /* has to be larger than 1050 => se > conflitto con modal! */
}
.fieldUpdateAjax.icon-true, .fieldUpdateAjax-disabled.icon-true {
	color: #2E8B57;
}
.fieldUpdateAjax.icon-false, .fieldUpdateAjax-disabled.icon-false {
	color: #dd4b39;
}

/*
 * stati ordine
 */
.action {
    padding-left: 40px;
    width: auto!important;
    display: inline-block;
    width: 32px;
    height: 32px;
    margin-right: 5px;
    float: left;
    cursor: hand;
}

.orderStatoCREATE-INCOMPLETE {
    background: url(/images/cake/actions/32x32/flag.png) no-repeat!important
}
.desSupplierStatoOPEN,
.orderStatoOPEN,
.orderStatoPRODGASPROMOTION-GAS-OPEN,
.orderStatoPRODGASPROMOTION-GAS-USERS-OPEN {
    background: url(/images/cake/cart/32x32/shopping_cart_basket.png) no-repeat!important
}
.orderStatoRI-OPEN-VALIDATE {
    background: url(/images/cake/cart/32x32/shopping_cart.png) no-repeat!important
}
.orderStatoPROCESSED-BEFORE-DELIVERY,
.orderStatoPROCESSED-POST-DELIVERY {
    background: url(/images/cake/cart/32x32/shopping_cart_accept_basket.png) no-repeat!important
}
.orderStatoINCOMING-ORDER {
    background: url(/images/cake/apps/32x32/ark.png) no-repeat!important
}
.orderStatoBEFORE-TRASMISSION,
.orderStatoTRASMISSION-TO-GAS,
.orderStatoPRODGASPROMOTION-GAS-TRASMISSION-TO-GAS {
    background: url(/images/cake/apps/32x32/mailreminder.png) no-repeat!important
}
.orderStatoFINISH,
.orderStatoPRODGASPROMOTION-GAS-FINISH, 
.orderStatoPOST-TRASMISSION {
    background: url(/images/cake/actions/32x32/lock.png) no-repeat!important
}
.orderStatoREFERENT-WORKING {
    background: url(/images/cake/actions/32x32/lock-silver.png) no-repeat!important
}
.desSupplierStatoWAITING,
.orderStatoOPEN-NEXT,
.orderStatoWAIT-PROCESSED-TESORIERE,
.orderStatoWAIT-REQUEST-PAYMENT-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_time.png) no-repeat!important
}
.desSupplierStatoOPEN-CLOSE,
.orderStatoPROCESSED-ON-DELIVERY,
.orderStatoPROCESSED-TESORIERE,
.orderStatoPROCESSED-TESORIERE-POST-DELIVERY,
.orderStatoWORKING,
.orderStatoPRODGASPROMOTION-GAS-WORKING,
.orderStatoPRODGASPROMOTION-GAS-USERS-WORKING {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_run.png) no-repeat!important
}
.orderStatoTO-REQUEST-PAYMENT, 
.orderStatoTO-PAYMENT {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_key.png) no-repeat!important
}
.desSupplierStatoCLOSE,
.orderStatoCLOSE,
.orderStatoPRODGASPROMOTION-GAS-CLOSE,
.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_delete_basket.png) no-repeat!important
}
.orderStatoUSER-PAID {
    background: url(/images/cake/apps/32x32/kspread.png) no-repeat!important
}
.orderStatoSUPPLIER-PAID {
    background: url(/images/cake/apps/32x32/calc.png) no-repeat!important
}
</style>