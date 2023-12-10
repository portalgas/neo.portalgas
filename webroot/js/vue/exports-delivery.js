"use strict";

var exports_delivery = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    exports_delivery = new Vue({
      router,
      el: '#vue-exports-delivery',
      data: {
        print_id: null,
        print_results: '',
        is_run: false,
        format: 'HTML'
      },  
      methods: {
        exportGets: function(e) {
          this.format = $("input[name='format']:checked").val();
          // workaround perche' al primo click non lo valorizzava!
          this.print_id = $("input[name='print_id']:checked").val();
          this.gets();
        },
        setInit: function(e) {
          this.format = 'HTML';
          this.print_id = null;
          this.print_results = '';
          this.is_run = false;
          $("input[name='print_id']:checked").prop('checked', false);
        },
        htmlGets: function(e) {
          this.format = 'HTML';
          // workaround perche' al primo click non lo valorizzava!
          this.print_id = $("input[name='print_id']:checked").val();
          this.print_results = '';
          this.is_run = true;
          this.gets();
        },
        gets: function(e) {

            let _this = this; 

            console.log('print_id '+this.print_id+' format '+this.format, 'gets');
            if(typeof this.print_id === 'undefined' || this.print_id==null) {
              alert("Scegli la tipologia di stampa");
              this.is_run = false;
              return; 
            }

            let delivery_id = $("select[name='delivery_id']").val();
            if(delivery_id=='') {
              alert("Scegli la consegna");
              this.is_run = false;
              return; 
            }

            $('#btn-export').prop('disabled', true);

            let $frm = $('#frm');
            switch(this.format) {
              case 'XLSX':
                $frm.attr('target', "_blank");
                $frm.attr('method', "POST");
                $frm.attr('action', "/admin/exports-xlsx/get");
                $frm.attr('action', "/admin/api/exports-delivery/get");   
               // $frm.attr('action', "/admin/tests/excel");          
                $frm.submit();

                $('#btn-export').prop('disabled', false);
              break;
              default:
                $frm.attr('target', "");
                $frm.attr('method', "POST");
                $frm.attr('action', ""); 
              break;
            }
            
            let params = {
                delivery_id: delivery_id,
                print_id: this.print_id,
                format: this.format              
            }; 
            /*
             * estraggo tutte le opzioni di stampa
             */
            let options = {}
            options['opts'] = {}
            let type = null;
            let name = null
            $.each($('.options'),function(i){
              type = $(this).attr('type');
              name = $(this).attr('name');
              console.log(name + ' ' + $(this).is(':checked') + ' => ' + $(this).val());
              if(type=='radio' && $(this).is(':checked')) {
                  options['opts'][name] = $(this).val()
              }
            });      
            params = Object.assign({}, params, options);
            console.log(params, 'gets params'); 

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

            let extra = '';
            switch(_this.format) {
              case 'HTML':
                extra = {headers: {
                              'Content-Type': 'application/json',
                              'Accept': 'application/json'
                            }}
              break;
              case 'PDF':
                extra = {responseType: 'blob',
                            headers: {
                              'Content-Type': 'application/json',
                              'Accept': 'application/pdf'
                            }}
              break;
            }

            axios.post('/admin/api/exports-delivery/get', params, extra)
                .then(response => {
                  // console.log(response.data, 'get');
                  switch(_this.format) {
                    case 'HTML':
                      _this.print_results = response.data;
                    break;
                    case 'PDF':
                      let headers = response.headers;
                      // console.log(headers, 'headers');
                      let filename = 'documento.pdf';
                      if(typeof headers.filename!=='undefined' && headers.filename!='') 
                        filename = headers.filename;
                                            
                        this.downloadBlob(response.data, filename);
                    break;
                  }
                  _this.is_run = false;
                  $('#btn-export').prop('disabled', false);
                })
            .catch(error => {
                  _this.is_run = false;
                  console.error("Error: " + error);
                  $('#btn-export').prop('disabled', false);
            });            
        },       
        downloadBlob(res, filename) {
          var blob = new Blob([res], { type: "application/pdf" });
          // console.log(blob, 'blob');
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', filename);
          document.body.appendChild(link);
          link.click();
          setTimeout(function () {
            window.URL.revokeObjectURL(url)
          }, 100)
        }        
      },
      filters: {
        html(text) {
          return text;
        },
      },       
      mounted: function(){
        console.log('mounted exports-delivery');
      }
    });
});