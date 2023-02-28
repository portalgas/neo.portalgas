"use strict";

var exports = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    exports = new Vue({
      router,
      el: '#vue-exports',
      data: {
        print_id: null,
        print_results: '',
        is_run: false,
        format: 'HTML'
      },  
      methods: {
        pdfGets: function(e) {
          this.format = 'PDF';
          // workaround perche' al primo click non lo valorizzava!
          this.print_id = $("input[name='print_id']:checked").val();
          this.gets();
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
            if(this.print_id==null)
              return; 

            let organization_id = $("input[name='organization_id']").val(); 
            let order_id = $("input[name='order_id']").val();
            let order_type_id = $("input[name='order_type_id']").val();
            
            let params = {
                organization_id: organization_id,
                order_type_id: order_type_id,
                order_id: order_id,
                print_id: this.print_id,
                format: this.format              
            }; 
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


            axios.post('/admin/api/exports-referents/get', params, extra)
                .then(response => {
                  // console.log(response.data, 'get');
                  switch(_this.format) {
                    case 'HTML':
                      _this.print_results = response.data;
                    break;
                    case 'PDF':
                      this.downloadBlob(response.data);
                    break;
                  }
                  _this.is_run = false;
                })
            .catch(error => {
                  _this.is_run = false;
                  console.error("Error: " + error);
            });            
        },       
        downloadBlob(res) {
          var blob = new Blob([res], { type: "application/pdf" });
          // console.log(blob, 'blob');
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'pdf-js.pdf');
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
        console.log('mounted exports');
      }
    });
});