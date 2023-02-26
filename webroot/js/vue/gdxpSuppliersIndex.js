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

            var _this = this;
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
                  _this.is_found_suppliers = true;
                  _this.suppliers = response.data.results;        
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
                        _this.is_found_suppliers = true;
                        _this.suppliers = response.data.results;        
                        
                        $('.msg-header').html("Attenzione");
                        $('.msg-body').html("Dati recuperati dal file locale perche' il servizio non e' disponibile!");
                      })
                  .catch(error => {
                        $('.run-suppliers .spinner').removeClass(ico_spinner);
                        _this.is_found_suppliers = false;
                        console.error("Error: " + error);
                  }); 
            });            
        }       
      },
      mounted: function(){
        console.log('mounted vueGdxpSuppliers');
        this.getGdxpSupplierIndex();
      },
      filters: {
        currency(amount) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          const amt = Number(amount);
          return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
        },
        /*
         * formatta l'importo float che arriva dal database
         * da 1000.5678 in 1.000,57 
         * da 1000 in 1.000,00          
         */
        formatImportToDb: function(number) {
              var decimals = 2;
              var dec_point = ','; 
              var thousands_sep = '.';

              console.log('formatImportToDb BEFORE number '+number);

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
              console.log('formatImportToDb AFTER number '+number);
              return number;
          },         
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        },
        counter: function (index) {
            return index+1
        }
      }      
    });
});