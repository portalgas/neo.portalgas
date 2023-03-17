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
.actionWorkflow-small {
    background: url(/images/cake/actions/24x24/canvas_holder.png) no-repeat
}
.actionView {
    background: url(/images/cake/actions/32x32/filenew.png) no-repeat
}
.actionView-small {
    background: url(/images/cake/actions/24x24/filenew.png) no-repeat
}
.actionEdit {
    background: url(/images/cake/actions/32x32/edit.png) no-repeat
}
.actionEdit-small {
    background: url(/images/cake/actions/24x24/edit.png) no-repeat
}
.actionJContent {
    background: url(/images/cake/apps/32x32/kontact.png) no-repeat
}
.actionJContent-small {
    background: url(/images/cake/apps/24x24/kontact.png) no-repeat
}
.actionEditCart {
    background: url(/images/cake/actions/32x32/edit_cart.png) no-repeat!important
}
.actionEditCart-small {
    background: url(/images/cake/actions/24x24/edit_cart.png) no-repeat!important
}
.actionEditDbGroupByUsers {
    background: url(/images/cake/apps/32x32/kexi.png) no-repeat
}
.actionEditDbGroupByUsers-small {
    background: url(/images/cake/apps/24x24/kexi.png) no-repeat
}
.actionEditDbOne {
    background: url(/images/cake/apps/32x32/kexi_one.png) no-repeat
}
.actionEditDbOne-small {
    background: url(/images/cake/apps/24x24/kexi_one.png) no-repeat
}
.actionEditDbSplit {
    background: url(/images/cake/apps/32x32/kexi_split.png) no-repeat
}
.actionEditDbSplit-small {
    background: url(/images/cake/apps/24x24/kexi_split.png) no-repeat
}
.actionDelete {
    background: url(/images/cake/actions/32x32/button_cancel.png) no-repeat!important
}
.actionDelete-small {
    background: url(/images/cake/actions/24x24/button_cancel.png) no-repeat!important
}
.actionAdd {
    background: url(/images/cake/actions/32x32/edit_add.png) no-repeat
}
.actionAdd-small {
    background: url(/images/cake/actions/24x24/edit_add.png) no-repeat
}
.actionRemove {
    background: url(/images/cake/actions/32x32/edit_remove.png) no-repeat
}
.actionRemove-small {
    background: url(/images/cake/actions/24x24/edit_remove.png) no-repeat
}
.actionPhone {
    background: url(/images/cake/apps/32x32/linphone.png) no-repeat
}
.actionPhone-small {
    background: url(/images/cake/apps/24x24/linphone.png) no-repeat
}
.actionSharing {
    background: url(/images/cake/apps/32x32/sharemanager.png) no-repeat
}
.actionSharing-small {
    background: url(/images/cake/apps/24x24/sharemanager.png) no-repeat
}
.actionValidate {
    background: url(/images/cake/apps/32x32/clean.png) no-repeat
}
.actionValidate-small {
    background: url(/images/cake/apps/24x24/clean.png) no-repeat
}
.actionTrasport {
    background: url(/images/cake/apps/32x32/ark2.png) no-repeat
}
.actionTrasport-small {
    background: url(/images/cake/apps/24x24/ark2.png) no-repeat
}
.actionCostMore {
    background: url(/images/cake/apps/32x32/kwallet2.png) no-repeat
}
.actionCostMore-small {
    background: url(/images/cake/apps/24x24/kwallet2.png) no-repeat
}
.actionCostLess {
    background: url(/images/cake/apps/32x32/kwallet.png) no-repeat
}
.actionCostLess-small {
    background: url(/images/cake/apps/32x32/kwallet.png) no-repeat
}
.actionIncomingOrder {
    background: url(/images/cake/apps/32x32/ark.png) no-repeat
}
.actionIncomingOrder-small {
    background: url(/images/cake/apps/24x24/ark.png) no-repeat
}
.actionNotIncomingOrder {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionNotIncomingOrder-small {
    background: url(/images/cake/actions/24x24/reload.png) no-repeat
}
.actionClose {
    background: url(/images/cake/actions/32x32/exit.png) no-repeat
}
.actionClose-small {
    background: url(/images/cake/actions/24x24/exit.png) no-repeat
}
.actionSyncronize {
    background: url(/images/cake/actions/32x32/syncronize.png) no-repeat;
}
.actionSyncronize-small {
    background: url(/images/cake/actions/24x24/syncronize.png) no-repeat;
}
.actionOnOff {
    background: url(/images/cake/actions/32x32/on-off.png) no-repeat;
}
.actionOnOff-small {
    background: url(/images/cake/actions/24x24/on-off.png) no-repeat;
}
.actionFromRefToTes {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionFromRefToTes-small {
    background: url(/images/cake/actions/24x24/reloadFoward.png) no-repeat
}
.actionFromTesToRef {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionFromTesToRef-small {
    background: url(/images/cake/actions/24x24/reload.png) no-repeat
}
.actionTrasmission {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionTrasmission-small {
    background: url(/images/cake/actions/24x24/reloadFoward.png) no-repeat
}
.actionReturnTrasmission {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionReturnTrasmission-small {
    background: url(/images/cake/actions/24x24/reload.png) no-repeat
}
.actionToStoreroom {
    background: url(/images/cake/apps/32x32/kwin4.png) no-repeat
}
.actionToStoreroom-small {
    background: url(/images/cake/apps/24x24/kwin4.png) no-repeat
}
.actionPrinter {
    background: url(/images/cake/apps/32x32/printer.png) no-repeat
}
.actionPrinter-small {
    background: url(/images/cake/apps/24x24/printer.png) no-repeat
}
.actionPrice {
    background: url(/images/cake/apps/32x32/calc.png) no-repeat
}
.actionPrice-small {
    background: url(/images/cake/apps/24x24/calc.png) no-repeat
}
.actionRun {
    background: url(/images/cake/apps/32x32/kservices.png) no-repeat
}
.actionRun-small {
    background: url(/images/cake/apps/24x24/kservices.png) no-repeat
}
.actionBackup {
    background: url(/images/cake/actions/32x32/db_comit.png) no-repeat
}
.actionBackup-small {
    background: url(/images/cake/actions/24x24/db_comit.png) no-repeat
}
.actionReload {
    background: url(/images/cake/actions/32x32/reload.png) no-repeat
}
.actionReload-small {
    background: url(/images/cake/actions/24x24/reload.png) no-repeat
}
.actionReloadFoward {
    background: url(/images/cake/actions/32x32/reloadFoward.png) no-repeat
}
.actionReloadFoward-small {
    background: url(/images/cake/actions/24x24/reloadFoward.png) no-repeat
}
.actionPay {
    background: url(/images/cake/apps/32x32/kspread.png) no-repeat
}
.actionPay-small {
    background: url(/images/cake/apps/24x24/kspread.png) no-repeat
}
.actionWaitPay {
    background: url(/images/cake/apps/32x32/file-manager.png) no-repeat
}
.actionWaitPay-small {
    background: url(/images/cake/apps/24x24/file-manager.png) no-repeat
}
.actionCopy {
    background: url(/images/cake/actions/32x32/editcopy.png) no-repeat
}
.actionCopy-small {
    background: url(/images/cake/actions/24x24/editcopy.png) no-repeat
}
.actionConfig {
    background: url(/images/cake/actions/32x32/config.png) no-repeat
}
.actionConfig-small {
    background: url(/images/cake/actions/24x24/config.png) no-repeat
}
.actionLog {
    background: url(/images/cake/apps/32x32/kpersonalizer.png) no-repeat
}
.actionLog-small {
    background: url(/images/cake/apps/24x24/kpersonalizer.png) no-repeat
}
.actionMenu {
    background: url(/images/cake/apps/32x32/menu.png) no-repeat!important;
    cursor: pointer
}
.actionMenu-small {
    background: url(/images/cake/apps/24x24/menu.png) no-repeat!important;
    cursor: pointer
}
.actionList {
    background: url(/images/cake/apps/32x32/cards.png) no-repeat!important;
    cursor: pointer
}
.actionList-small {
    background: url(/images/cake/apps/24x24/cards.png) no-repeat!important;
    cursor: pointer
}
.actionQuick {
    background: url(/images/cake/apps/32x32/clicknrun.png) no-repeat!important;
    cursor: pointer
}
.actionQuick-small {
    background: url(/images/cake/apps/24x24/clicknrun.png) no-repeat!important;
    cursor: pointer
}
.actionOpen {
    background: url(/images/cake/actions/32x32/open_store.png) no-repeat!important;
    cursor: pointer
}
.actionOpen-small {
    background: url(/images/cake/actions/24x24/open_store.png) no-repeat!important;
    cursor: pointer
}
.actionDeliveryCalendar {
    background: url(/images/cake/apps/32x32/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionDeliveryCalendar-small {
    background: url(/images/cake/apps/24x24/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionDes {
    background: url(/images/cake/apps/32x32/Community_Help.png) no-repeat!important;
    cursor: pointer
}
.actionDes-small {
    background: url(/images/cake/apps/24x24/Community_Help.png) no-repeat!important;
    cursor: pointer
}
.actionPdf {
    background: url(/images/cake/minetypes/32x32/pdf.png) no-repeat!important;
    cursor: pointer
}
.actionPdf-small {
    background: url(/images/cake/minetypes/24x24/pdf.png) no-repeat!important;
    cursor: pointer
}
.actionCsv {
    background: url(/images/cake/minetypes/32x32/spreadsheet.png) no-repeat!important;
    cursor: pointer
}
.actionCsv-small {
    background: url(/images/cake/minetypes/24x24/spreadsheet.png) no-repeat!important;
    cursor: pointer
}
.actionExcel {
    background: url(/images/cake/minetypes/32x32/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionExcel-small {
    background: url(/images/cake/minetypes/24x24/vcalendar.png) no-repeat!important;
    cursor: pointer
}
.actionMail {
    background: url(/images/cake/apps/32x32/mailreminder.png) no-repeat!important;
    cursor: pointer
}
.actionMail-small {
    background: url(/images/cake/apps/24x24/mailreminder.png) no-repeat!important;
    cursor: pointer
}

/*
 * stati ordine
 */
.orderStatoCREATE-INCOMPLETE {
    background: url(/images/cake/actions/32x32/flag.png) no-repeat!important
}
.orderStatoCREATE-INCOMPLETE-small {
    background: url(/images/cake/actions/24x24/flag.png) no-repeat!important
}
.desSupplierStatoOPEN,
.orderStatoOPEN,
.orderStatoPRODGASPROMOTION-GAS-OPEN,
.orderStatoPRODGASPROMOTION-GAS-USERS-OPEN {
    background: url(/images/cake/cart/32x32/shopping_cart_basket.png) no-repeat!important
}
.desSupplierStatoOPEN-small,
.orderStatoOPEN-small,
.orderStatoPRODGASPROMOTION-GAS-OPEN-small,
.orderStatoPRODGASPROMOTION-GAS-USERS-OPEN-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket.png) no-repeat!important
}
.orderStatoRI-OPEN-VALIDATE {
    background: url(/images/cake/cart/32x32/shopping_cart.png) no-repeat!important
}
.orderStatoRI-OPEN-VALIDATE-small {
    background: url(/images/cake/cart/24x24/shopping_cart.png) no-repeat!important
}
.orderStatoPROCESSED-BEFORE-DELIVERY,
.orderStatoPROCESSED-POST-DELIVERY {
    background: url(/images/cake/cart/32x32/shopping_cart_accept_basket.png) no-repeat!important
}
.orderStatoPROCESSED-BEFORE-DELIVERY-small,
.orderStatoPROCESSED-POST-DELIVERY-small {
    background: url(/images/cake/cart/24x24/shopping_cart_accept_basket.png) no-repeat!important
}
.orderStatoINCOMING-ORDER {
    background: url(/images/cake/apps/32x32/ark.png) no-repeat!important
}
.orderStatoINCOMING-ORDER-small {
    background: url(/images/cake/apps/24x24/ark.png) no-repeat!important
}
.orderStatoBEFORE-TRASMISSION,
.orderStatoTRASMISSION-TO-GAS,
.orderStatoPRODGASPROMOTION-GAS-TRASMISSION-TO-GAS {
    background: url(/images/cake/apps/32x32/mailreminder.png) no-repeat!important
}
.orderStatoBEFORE-TRASMISSION-small,
.orderStatoTRASMISSION-TO-GAS-small,
.orderStatoPRODGASPROMOTION-GAS-TRASMISSION-TO-GAS-small {
    background: url(/images/cake/apps/24x24/mailreminder.png) no-repeat!important
}
.orderStatoFINISH,
.orderStatoPRODGASPROMOTION-GAS-FINISH, 
.orderStatoPOST-TRASMISSION {
    background: url(/images/cake/actions/32x32/lock.png) no-repeat!important
}
.orderStatoFINISH-small,
.orderStatoPRODGASPROMOTION-GAS-FINISH-small, 
.orderStatoPOST-TRASMISSION-small {
    background: url(/images/cake/actions/24x24/lock.png) no-repeat!important
}
.orderStatoREFERENT-WORKING {
    background: url(/images/cake/actions/32x32/lock-silver.png) no-repeat!important
}
.orderStatoREFERENT-WORKING-small {
    background: url(/images/cake/actions/24x24/lock-silver.png) no-repeat!important
}
.desSupplierStatoWAITING,
.orderStatoOPEN-NEXT,
.orderStatoWAIT-PROCESSED-TESORIERE,
.orderStatoWAIT-REQUEST-PAYMENT-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_time.png) no-repeat!important
}
.desSupplierStatoWAITING-small,
.orderStatoOPEN-NEXT-small,
.orderStatoWAIT-PROCESSED-TESORIERE-small,
.orderStatoWAIT-REQUEST-PAYMENT-CLOSE-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_time.png) no-repeat!important
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
.desSupplierStatoOPEN-CLOSE-small,
.orderStatoPROCESSED-ON-DELIVERY-small,
.orderStatoPROCESSED-TESORIERE-small,
.orderStatoPROCESSED-TESORIERE-POST-DELIVERY-small,
.orderStatoWORKING-small,
.orderStatoPRODGASPROMOTION-GAS-WORKING-small,
.orderStatoPRODGASPROMOTION-GAS-USERS-WORKING-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_run.png) no-repeat!important
}
.orderStatoTO-REQUEST-PAYMENT, 
.orderStatoTO-PAYMENT {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_key.png) no-repeat!important
}
.orderStatoTO-REQUEST-PAYMENT-small, 
.orderStatoTO-PAYMENT-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_key.png) no-repeat!important
}
.desSupplierStatoCLOSE,
.orderStatoCLOSE,
.orderStatoPRODGASPROMOTION-GAS-CLOSE,
.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_delete_basket.png) no-repeat!important
}
.desSupplierStatoCLOSE-small,
.orderStatoCLOSE-small,
.orderStatoPRODGASPROMOTION-GAS-CLOSE-small,
.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE-small {
    background: url(/images/cake/cart/24x24/shopping_cart_delete_basket.png) no-repeat!important
}
.orderStatoUSER-PAID {
    background: url(/images/cake/apps/32x32/kspread.png) no-repeat!important
}
.orderStatoUSER-PAID-small {
    background: url(/images/cake/apps/24x24/kspread.png) no-repeat!important
}
.orderStatoSUPPLIER-PAID {
    background: url(/images/cake/apps/32x32/calc.png) no-repeat!important
}
.orderStatoSUPPLIER-PAID-small {
    background: url(/images/cake/apps/24x24/calc.png) no-repeat!important
}

/*
 * menu ordine
 */ 
ul.menuLateraleItems {
    list-style: none;
    margin: 0;
    padding: 0;
}
ul.menuLateraleItems li {
    clear: both;
    /* height: 32px;*/
    list-style-type: none;
    margin: 15px 0;
    padding: 0 10px;
    white-space: normal; /* nowrap */
}
ul.menuLateraleItems li span {
    display: block;
    /* height: 32px; */
    width: 32px;
}
ul.menuLateraleItems li a.menu-item,
ul.menuLateraleItems li div {
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
ul.menuLateraleItems .actionEditCart-small {
    background: url(/images/cake/actions/24x24/edit_cart.png) no-repeat
}
ul.menuLateraleItems .actionEditDbGroupByUsers {
    background: url(/images/cake/apps/32x32/kexi.png) no-repeat
}
ul.menuLateraleItems .actionEditDbGroupByUsers-small {
    background: url(/images/cake/apps/24x24/kexi.png) no-repeat
}
ul.menuLateraleItems .actionEditDbOne {
    background: url(/images/cake/apps/32x32/kexi_one.png) no-repeat
}
ul.menuLateraleItems .actionEditDbOne-small {
    background: url(/images/cake/apps/24x24/kexi_one.png) no-repeat
}
ul.menuLateraleItems .actionEditDbSplit {
    background: url(/images/cake/apps/32x32/kexi_split.png) no-repeat
}
ul.menuLateraleItems .actionEditDbSplit-small {
    background: url(/images/cake/apps/24x24/kexi_split.png) no-repeat
}
ul.menuLateraleItems .actionValidate {
    background: url(/images/cake/apps/32x32/clean.png) no-repeat
}
ul.menuLateraleItems .actionValidate-small {
    background: url(/images/cake/apps/24x24/clean.png) no-repeat
}
ul.menuLateraleItems a.actionPrinter {
    background: url(/images/cake/apps/32x32/printer.png) no-repeat
}
ul.menuLateraleItems a.actionPrinter-small {
    background: url(/images/cake/apps/24x24/printer.png) no-repeat
}
ul.menuLateraleItems .actionClose {
    background: url(/images/cake/actions/32x32/exit.png) no-repeat
}
ul.menuLateraleItems .actionClose-small {
    background: url(/images/cake/actions/24x24/exit.png) no-repeat
}
ul.menuLateraleItems .orderStatoCREATE-INCOMPLETE,
ul.menuLateraleItems a.orderStatoCREATE-INCOMPLETE {
    background: url(/images/cake/actions/32x32/flag.png) no-repeat
}
ul.menuLateraleItems .orderStatoCREATE-INCOMPLETE-small,
ul.menuLateraleItems a.orderStatoCREATE-INCOMPLETE-small {
    background: url(/images/cake/actions/24x24/flag.png) no-repeat
}
ul.menuLateraleItems .orderStatoOPEN,
ul.menuLateraleItems a.orderStatoOPEN {
    background: url(/images/cake/cart/32x32/shopping_cart_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoOPEN-small,
ul.menuLateraleItems a.orderStatoOPEN-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoRI-OPEN-VALIDATE,
ul.menuLateraleItems a.orderStatoRI-OPEN-VALIDATE {
    background: url(/images/cake/cart/32x32/shopping_cart.png) no-repeat
}
ul.menuLateraleItems .orderStatoRI-OPEN-VALIDATE-small,
ul.menuLateraleItems a.orderStatoRI-OPEN-VALIDATE-small {
    background: url(/images/cake/cart/24x24/shopping_cart.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY,
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-POST-DELIVERY,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-POST-DELIVERY {
    background: url(/images/cake/cart/32x32/shopping_cart_accept_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY-small,
ul.menuLateraleItems .orderStatoPROCESSED-REFERENTE-POST-DELIVERY-small,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-BEFORE-DELIVERY-small,
ul.menuLateraleItems a.orderStatoPROCESSED-REFERENTE-POST-DELIVERY-small {
    background: url(/images/cake/cart/24x24/shopping_cart_accept_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoWAIT-PROCESSED-TESORIERE,
ul.menuLateraleItems .orderStatoOPEN-NEXT,
ul.menuLateraleItems .orderStatoWAIT-REQUEST-PAYMENT-CLOSE,
ul.menuLateraleItems a.orderStatoWAIT-PROCESSED-TESORIERE,
ul.menuLateraleItems a.orderStatoOPEN-NEXT,
ul.menuLateraleItems a.orderStatoWAIT-REQUEST-PAYMENT-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_time.png) no-repeat
}
ul.menuLateraleItems .orderStatoWAIT-PROCESSED-TESORIERE-small,
ul.menuLateraleItems .orderStatoOPEN-NEXT-small,
ul.menuLateraleItems .orderStatoWAIT-REQUEST-PAYMENT-CLOSE-small,
ul.menuLateraleItems a.orderStatoWAIT-PROCESSED-TESORIERE-small,
ul.menuLateraleItems a.orderStatoOPEN-NEXT-small,
ul.menuLateraleItems a.orderStatoWAIT-REQUEST-PAYMENT-CLOSE {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_time.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE,
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE-POST-DELIVERY,
ul.menuLateraleItems .orderStatoWORKING,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE-POST-DELIVERY,
ul.menuLateraleItems a.orderStatoWORKING {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_run.png) no-repeat
}
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE-small,
ul.menuLateraleItems .orderStatoPROCESSED-TESORIERE-POST-DELIVERY-small,
ul.menuLateraleItems .orderStatoWORKING-small,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE-small,
ul.menuLateraleItems a.orderStatoPROCESSED-TESORIERE-POST-DELIVERY-small,
ul.menuLateraleItems a.orderStatoWORKING-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_run.png) no-repeat
}
ul.menuLateraleItems .orderStatoTO-PAYMENT,
ul.menuLateraleItems a.orderStatoTO-PAYMENT {
    background: url(/images/cake/cart/32x32/shopping_cart_basket_key.png) no-repeat
}
ul.menuLateraleItems .orderStatoTO-PAYMENT-small,
ul.menuLateraleItems a.orderStatoTO-PAYMENT-small {
    background: url(/images/cake/cart/24x24/shopping_cart_basket_key.png) no-repeat
}
ul.menuLateraleItems .orderStatoCLOSE,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-CLOSE,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE,
ul.menuLateraleItems a.orderStatoCLOSE,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-CLOSE,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE {
    background: url(/images/cake/cart/32x32/shopping_cart_delete_basket.png) no-repeat
}
ul.menuLateraleItems .orderStatoCLOSE-small,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-CLOSE-small,
ul.menuLateraleItems .orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE-small,
ul.menuLateraleItems a.orderStatoCLOSE-small,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-CLOSE-small,
ul.menuLateraleItems a.orderStatoPRODGASPROMOTION-GAS-USERS-CLOSE-small {
    background: url(/images/cake/cart/24x24/shopping_cart_delete_basket.png) no-repeat
}
ul.menuLateraleItems .bgLeft {
    padding: 0px 0px 0px 40px;
    background-position: top left!important
}
ul.menuLateraleItems .bgLeftModal {
    padding: 16px 0px 16px 40px;
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
    color: #666;
    margin: 15px 0;
    padding: 0 15px;
    white-space: normal;
}
ul.workflow li span.statoNotCurrent a {
    opacity: .5;
    margin: 15px 0;
    padding: 0 15px;
    white-space: normal;    
}

/* 
 * leggenda 
 */
.legenda {
    background-color: #fff;
    border: 1px solid #DEDEDE;
    border-radius: 8px 8px 8px 8px;
    clear: both;
    margin: 10px 0;
    padding: 10px;
    min-height: 30px;
}
.legenda div {
    margin: 5px 0 5px 0;
}

.legenda table tr td {
    border: none
}
.legenda table tr td h4 {
    text-align: center;
    background-color: #C3D2E5;
    border-bottom: 3px solid #84A7DB;
    border-top: 3px solid #84A7DB;
    color: #FFF
}
.legenda-ico-mails {
    background: url(/images/cake/apps/32x32/xfmail.png) 7px 7px no-repeat #C3D2E5;
    float: right;
    padding-left: 55px;
    width: 75%
}
.legenda-ico-info {
    background: url(/images/cake/actions/32x32/info.png) 7px 7px no-repeat #C3D2E5;
    float: right;
    padding-left: 55px;
    width: 75%
}
.legenda-ico-alert {
    background: url(/images/cake/apps/32x32/error.png) 7px 7px no-repeat #C3D2E5;
    float: right;
    padding-left: 55px;
    width: 75%
}
 

.stato_si, .stato_0, .stato_y, .stato_open {
   background: url('/images/cake/icons/16x16/tick.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}
.stato_no, .stato_1, .stato_n, .stato_close {
   background: url('/images/cake/icons/16x16/cross.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}
.stato_si_int, .stato_0_int, .stato_y_int, .stato_open_int {
   background: url('/images/cake/icons/16x16/tick.png') no-repeat scroll left center transparent;
   padding-left: 20px;
}
.stato_no_int, .stato_1_int, .stato_n_int, .stato_close_int {
   background: url('/images/cake/icons/16x16/cross.png') no-repeat scroll left center transparent;
   padding-left: 20px;
}
.stato_wait {
   background: url('/images/cake/icons/16x16/clock_red.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}
.stato_lock {
   background: url('/images/cake/icons/16x16/lock.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}
.stato_qtamax {
   background: url('/images/cake/icons/16x16/basket_delete.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}
.stato_t, .stato_temporaneo {
   background: url('/images/cake/icons/16x16/eye.png') no-repeat scroll center center transparent;
   height: 16px;
   padding-left: 8px;
}

</style>