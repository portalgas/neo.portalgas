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
 * style 
 */ 
h1,
h2,
h3,
h4 {
    font-weight: 400;
    margin-bottom: .5em
}
h1 {
    background: #fff;
    color: #003d4c;
    font-size: 18px
}
h2 {
    color: #e32;
    font-size: 16px;
    border-radius: 0px;
    -moz-border-radius: 0px;
    -webkit-border-radius: 0px;
    min-height: 42px;
    margin: 0px;
    padding: 10px;
    clear: both;
}
h3 {
    color: #2c6877;
    font-size: 15px
}
h4 {
    color: #993;
    font-weight: 400
}

/* 
 * actions
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

.actionWorkflow {
    background: url(/images/cake/actions/32x32/canvas_holder.png) no-repeat
}
.actionView {
    background: url(/images/cake/actions/32x32/filenew.png) no-repeat
}
.actionEdit {
    background: url(/images/cake/actions/32x32/edit.png) no-repeat
}
.actionJContent {
    background: url(/images/cake/apps/32x32/kontact.png) no-repeat
}
.actionEditCart {
    background: url(/images/cake/actions/32x32/edit_cart.png) no-repeat!important
}
.actionEditDbGroupByUsers {
    background: url(/images/cake/apps/32x32/kexi.png) no-repeat
}
.actionEditDbOne {
    background: url(/images/cake/apps/32x32/kexi_one.png) no-repeat
}
.actionEditDbSplit {
    background: url(/images/cake/apps/32x32/kexi_split.png) no-repeat
}
.actionDelete {
    background: url(/images/cake/actions/32x32/button_cancel.png) no-repeat!important
}
.actionAdd {
    background: url(/images/cake/actions/32x32/edit_add.png) no-repeat
}
.actionRemove {
    background: url(/images/cake/actions/32x32/edit_remove.png) no-repeat
}
.actionPhone {
    background: url(/images/cake/apps/32x32/linphone.png) no-repeat
}
.actionSharing {
    background: url(/images/cake/apps/32x32/sharemanager.png) no-repeat
}
.actionValidate {
    background: url(/images/cake/apps/32x32/clean.png) no-repeat
}
.actionTrasport {
    background: url(/images/cake/apps/32x32/ark2.png) no-repeat
}
.actionCostMore {
    background: url(/images/cake/apps/32x32/kwallet2.png) no-repeat
}
.actionCostLess {
    background: url(/images/cake/apps/32x32/kwallet.png) no-repeat
}
.actionIncomingOrder {
    background: url(/images/cake/apps/32x32/ark.png) no-repeat
}
.actionNotIncomingOrder {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionClose {
    background: url(/images/cake/actions/32x32/exit.png) no-repeat
}
.actionSyncronize {
    background: url(/images/cake/actions/32x32/syncronize.png) no-repeat;
}
.actionOnOff {
    background: url(/images/cake/actions/32x32/on-off.png) no-repeat;
}
.actionFromRefToTes {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionFromTesToRef {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionTrasmission {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionReturnTrasmission {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionToStoreroom {
    background: url(/images/cake/apps/32x32/kwin4.png) no-repeat
}
.actionPrinter {
    background: url(/images/cake/apps/32x32/printer.png) no-repeat
}
.actionPrice {
    background: url(/images/cake/apps/32x32/calc.png) no-repeat
}
.actionRun {
    background: url(/images/cake/apps/32x32/kservices.png) no-repeat
}
.actionBackup {
    background: url(/images/cake/actions/32x32/db_comit.png) no-repeat
}
.actionReload {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionReloadFoward {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionPay {
    background: url(/images/cake/apps/32x32/kspread.png) no-repeat
}
.actionWaitPay {
    background: url(/images/cake/apps/32x32/file-manager.png) no-repeat
}
.actionCopy {
    background: url(/images/cake/actions/32x32/editcopy.png) no-repeat
}
.actionConfig {
    background: url(/images/cake/actions/32x32/config.png) no-repeat
}
.actionLog {
    background: url(/images/cake/apps/32x32/kpersonalizer.png) no-repeat
}
.actionMenu {
    background: url(/images/cake/apps/32x32/menu.png) no-repeat!important;
    cursor: pointer
}
.actionList {
    background: url(/images/cake/apps/32x32/cards.png) no-repeat!important;
    cursor: pointer
}
.actionQuick {
    background: url(/images/cake/apps/32x32/clicknrun.png) no-repeat!important;
    cursor: pointer
}
.actionOpen {
    background: url(/images/cake/actions/32x32/open_store.png) no-repeat!important;
    cursor: pointer
}
.actionDeliveryCalendar {
    background: url(/images/cake/apps/32x32/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionDes {
    background: url(/images/cake/apps/32x32/Community_Help.png) no-repeat!important;
    cursor: pointer
}
.actionPdf {
    background: url(/images/cake/minetypes/32x32/pdf.png) no-repeat!important;
    cursor: pointer
}
.actionCsv {
    background: url(/images/cake/minetypes/32x32/spreadsheet.png) no-repeat!important;
    cursor: pointer
}
.actionExcel {
    background: url(/images/cake/minetypes/32x32/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionMail {
    background: url(/images/cake/apps/32x32/mailreminder.png) no-repeat!important;
    cursor: pointer
}

/*
 * stati ordine
 */
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


/*
 * menu ordine
 */ 
ul.menuLateraleItems {
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 12px;
}
ul.menuLateraleItems li {
    clear: both;
    /*height: 32px;*/
    list-style-type: none;
    margin: 2px 0;
    padding: 0 15px;
    white-space: nowrap
}
ul.menuLateraleItems li span {
    display: block;
    /* height: 32px; */
    width: 32px;
}
ul.menuLateraleItems li a,
ul.menuLateraleItems li div {
    font-weight: 400;
    display: block;
    clear: both;
    height: 32px
}

.menuOrderStatoTitle {
    background-color: #0a659e;
    color:#fff;
    font-size: 18px;
    text-align:center;
}

ul.menuLateraleItems .actionEditCart {
    background: url(/images/cake/actions/32x32/edit_cart.png) no-repeat
}
ul.menuLateraleItems .actionEditDbGroupByUsers {
    background: url(/images/cake/apps/32x32/kexi.png) no-repeat
}
ul.menuLateraleItems .actionEditDbOne {
    background: url(/images/cake/apps/32x32/kexi_one.png) no-repeat
}
ul.menuLateraleItems .actionEditDbSplit {
    background: url(/images/cake/apps/32x32/kexi_split.png) no-repeat
}
ul.menuLateraleItems .actionValidate {
    background: url(/images/cake/apps/32x32/clean.png) no-repeat
}
ul.menuLateraleItems a.actionPrinter {
    background: url(/images/cake/apps/32x32/printer.png) no-repeat
}
ul.menuLateraleItems .actionClose {
    background: url(/images/cake/actions/32x32/exit.png) no-repeat
}
ul.menuLateraleItems .orderStatoCREATE-INCOMPLETE,
ul.menuLateraleItems a.orderStatoCREATE-INCOMPLETE {
    background: url(/images/cake/actions/32x32/flag.png) no-repeat
}
ul.menuLateraleItems .orderStatoOPEN,
ul.menuLateraleItems a.orderStatoOPEN {
    background: url(/images/cake/cart/32x32/shopping_cart_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoRI-OPEN-VALIDATE,
ul.menuLateraleItems a.orderStatoRI-OPEN-VALIDATE {
    background: url(/images/cake/cart/32x32/shopping_cart.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY,
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-POST-DELIVERY,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-POST-DELIVERY,
\ {
    background: url(/images/cake/cart/32x32/shopping_cart_accept_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoWAIT-PROCESSED-TESORIERE,
ul.menuLateraleItems .orderStatoOPEN-NEXT,
ul.menuLateraleItems .orderStatoWAIT-REQUEST-PAYMENT-CLOSE,
ul.menuLateraleItems a.orderStatoWAIT-PROCESSED-TESORIERE,
ul.menuLateraleItems a.orderStatoOPEN-NEXT,
ul.menuLateraleItems a.orderStatoWAIT-REQUEST-PAYMENT-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_time.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE,
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE-POST-DELIVERY,
ul.menuLateraleItems .orderStatoWORKING,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE-POST-DELIVERY,
ul.menuLateraleItems a.orderStatoWORKING {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_run.png) no-repeat
}
ul.menuLateraleItems .orderStatoTO-PAYMENT,
ul.menuLateraleItems a.orderStatoTO-PAYMENT {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_key.png) no-repeat
}
ul.menuLateraleItems .orderStatoCLOSE,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-CLOSE,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE,
ul.menuLateraleItems a.orderStatoCLOSE,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-CLOSE,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_delete_basket.png) no-repeat
}
ul.menuLateraleItems .bgLeft {
    padding: 0 5px 0 40px;
    background-position: top left!important
}
ul.menuLateraleItems .bgRight {
    padding: 0 40px 0 5px;
    background-position: top right!important
}   

.statoCurrent {
    background-image: -moz-linear-gradient(top, #fc0, #e6b800)!important;
    background-image: -ms-linear-gradient(top, #fc0, #e6b800)!important;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#fc0), to(#e6b800))!important;
    background-image: -webkit-linear-gradient(top, #fc0, #e6b800)!important;
    background-image: -o-linear-gradient(top, #fc0, #e6b800)!important;
    background-image: linear-gradient(top, #fc0, #e6b800)!important;
    border-color: #EFDD11!important;
}
ul.workflow li span.statoCurrent a {
    color: #666
}
ul.workflow li span.statoNotCurrent a {
    opacity: .5
}
</style>