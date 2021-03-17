"use strict";

var testAjax = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    testAjax = new Vue({
      router,
      el: '#vue-test-ajax',
      data: {
        errors: [],
        esito: null,
        is_run: false
      },  
      methods: {
        submit: function(e) {

            e.preventDefault();

            var _this = this;
            _this.esito = null;
            _this.is_run = false;

            let service_url = $("select[name='service_url']").val();
            let delivery_id = $("select[name='delivery_id']").val();
            let order_id = $("select[name='order_id']").val();
            console.log('service_url '+service_url+' delivery_id '+delivery_id+' order_id '+order_id);

            if(service_url==0 || service_url=='') {
                $('.run-submit .spinner').removeClass(ico_spinner);
                // $('#submit').addClass('disabled');
                // $('#submit').prop("disabled", true);
                return;
            }

            $('.run-submit').show();
            $('.run-submit .spinner').addClass(ico_spinner);

            let params = {
                delivery_id: delivery_id,
                order_id: order_id
            }; 

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

            axios.post(service_url, params)
                .then(response => {
                  console.log(response.data);
                  $('.run-submit .spinner').removeClass(ico_spinner);
                  _this.is_run = true;
                  _this.esito = response.data;        

                })
            .catch(error => {
                  $('.run-submit .spinner').removeClass(ico_spinner);
                  _this.is_run = false;
                  console.error("Error: " + error);
            });            
        },
      },
      mounted: function(){
        console.log('mounted testAjax');
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