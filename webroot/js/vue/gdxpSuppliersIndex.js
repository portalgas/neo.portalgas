"use strict";

var vueGdxpSuppliers = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    vueGdxpSuppliers = new Vue({
      router,
      el: '#vue-suppliers',
      data: {
        errors: [],
        suppliers: null,
        is_found_suppliers: false
      },  
      methods: {
        getGdxpSupplierIndex: function(e) {

            this.is_found_suppliers = false;

            console.log(ajaxUrlRemoteGdxpSupplierIndex);

            $('.run-suppliers').show();
            $('.run-suppliers .spinner').addClass(ico_spinner);

            // axios.defaults.headers.common['Access-Control-Allow-Origin'] = 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, X-Auth-Token';

            var headers = {
                  'Access-Control-Allow-Origin': '*',
                  'Access-Control-Allow-Methods': 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
                  'Access-Control-Allow-Headers': '*'
            }

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  
            
            axios.get(ajaxUrlRemoteGdxpSupplierIndex)
                .then(response => {
                  console.log(response.data); 
                  $('.run-suppliers .spinner').removeClass(ico_spinner);
                  this.is_found_suppliers = true;
                  this.suppliers = response.data.results;        
                  // $('#submit').removeClass('disabled');
                })
            .catch(error => {
                  /*
                   * se non lo trova 
                   * has been blocked by CORS policy: Request header field x-csrf-token is not allowed by Access-Control-Allow-Headers in preflight response.
                   * prendo quello locale
                   */
                  axios.get(ajaxUrlLocalGdxpSupplierIndex)
                      .then(response => {
                        console.log(response.data); 
                        $('.run-suppliers .spinner').removeClass(ico_spinner);
                        this.is_found_suppliers = true;
                        this.suppliers = response.data.results;        
                        
                        $('.msg-header').html("Attenzione");
                        $('.msg-body').html("Dati recuperati dal file locale perche' il servizio non e' disponibile!");
                      })
                  .catch(error => {
                        $('.run-suppliers .spinner').removeClass(ico_spinner);
                        this.is_found_suppliers = false;
                        console.log("Error: " + error);
                  }); 
            });            
        }       
      },
      mounted: function(){
        console.log('mounted vueGdxpSuppliers');
        this.getGdxpSupplierIndex();
      },
      filters: {
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        }
      }      
    });
});