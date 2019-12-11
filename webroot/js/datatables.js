$(function () {
      $('.dataTables').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
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
  });