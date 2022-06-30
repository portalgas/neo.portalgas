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
echo $this->Html->css('my.min');

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
</style>