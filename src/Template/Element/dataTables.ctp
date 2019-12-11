<?php 
use Cake\Core\Configure; 

echo $this->Html->script('AdminLTE./bower_components/datatables.net/js/jquery.dataTables.min', ['block' => 'script']);

echo $this->Html->script('AdminLTE./bower_components/datatables.net-bs/js/dataTables.bootstrap.min', ['block' => 'script']); 

$this->Html->script('datatables', ['block' => 'scriptBottom']);   
?>
<style>
.paginator {
  display:none
}
</style>