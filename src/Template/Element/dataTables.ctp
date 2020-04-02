<?php 
use Cake\Core\Configure; 

if(!isset($paging))
	$paging = 'true';

if(!isset($ordering))
	$ordering = 'true';

echo $this->Html->script('AdminLTE./bower_components/datatables.net/js/jquery.dataTables.min', ['block' => 'script']);

echo $this->Html->script('AdminLTE./bower_components/datatables.net-bs/js/dataTables.bootstrap.min', ['block' => 'script']); 

$js = "
$(function () {
      $('.dataTables').DataTable({
        'paging'      : ".$paging.",
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : ".$ordering.",
        'info'        : true,
        'autoWidth'   : true,
        'language': {
            'url': '/lang/it_IT.json'
        }, 
        columnDefs: [
           { orderable: false, targets: -1 }
        ]
        // 'dom': '<\"top\"fli>rt<\"bottom\"p><\"clear\">'     
      })
  });";

$this->Html->scriptBlock($js, ['block' => true]);
// $this->Html->script('datatables', ['block' => 'scriptBottom']);   
?>
<style>
.paginator-disabled {
  display:none
}
</style>