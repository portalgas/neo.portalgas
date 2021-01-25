"use strict";

var vueCashiers = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    var ajaxUrlGetOrdersByDelivery = '/admin/api/orders/getByDelivery';
    var ajaxUrlGetUsersByDelivery = '/admin/api/carts/getUsersCashByDelivery';
    var ajaxUrlGetCompleteUsersByDelivery = '/admin/api/cashiers/getCompleteUsersByDelivery';
    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    vueCashiers = new Vue({
      router,
      el: '#vue-cashiers',
      data: {
        errors: [],
        orders: null,
        users: null,
        is_cash: '1', 
        is_found_orders: false,
        is_found_users: false   
      },  
      methods: {
        setIsCash: function(e) {

        },
        getData: function(e) {
           this.getOrdersByDelivery();
           this.getCompleteUsersByDelivery();
        },
        getOrdersByDelivery: function(e) {

            this.is_found_orders = false;

            let orders_state_code = 'PROCESSED-ON-DELIVERY';
            let delivery_id = $("select[name='delivery_id']").val();
            // console.log(ajaxUrlGetOrdersByDelivery+' delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.run-orders .spinner').removeClass(ico_spinner);
                // $('#submit').addClass('disabled');
                // $('#submit').prop("disabled", true);
                return;
            }

            $('.run-orders').show();
            $('.run-orders .spinner').addClass(ico_spinner);

            let params = {
                delivery_id: delivery_id,
                orders_state_code: orders_state_code
            }; 

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

            axios.post(ajaxUrlGetOrdersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  $('.run-orders .spinner').removeClass(ico_spinner);
                  this.is_found_orders = true;
                  this.orders = response.data;        
                  // $('#submit').removeClass('disabled');
                  // $('#submit').prop("disabled", false);
                })
            .catch(error => {
                  $('.run-orders .spinner').removeClass(ico_spinner);
                  this.is_found_orders = false;
                  console.error("Error: " + error);
            });            
        },
        getCompleteUsersByDelivery: function(e) {

            this.users = null;
            this.is_found_users = false;

            let delivery_id = $("select[name='delivery_id']").val();
            // console.log(ajaxUrlGetCompleteUsersByDelivery+' delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.run-users .spinner').removeClass(ico_spinner); 
                $('#submit').addClass('disabled');
                $('#submit').prop("disabled", true);
                return;
            }

            $('.run-users').show();
            $('.run-users .spinner').addClass(ico_spinner);

            let params = {
                delivery_id: delivery_id
            }; 

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

            axios.post(ajaxUrlGetCompleteUsersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  $('.run-users .spinner').removeClass(ico_spinner);
                  this.is_found_users = true;
                  this.users = response.data;

                  if(this.users.length>0) {
                    $('#submit').removeClass('disabled');
                    $('#submit').prop("disabled", false);
                  }
                  else {
                    $('#submit').addClass('disabled');
                    $('#submit').prop("disabled", true);
                  }
                })
            .catch(error => {
                 $('.run-users .spinner').removeClass(ico_spinner);
                 this.is_found_users = false;
                 console.error("Error: " + error);
            });            
        }        
      },
      mounted: function(){
        // console.log('mounted vueCasheirs');
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

              // console.log('formatImportToDb BEFORE number '+number);

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
              // console.log('formatImportToDb AFTER number '+number);

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
        },
        orderStateCode(state_code) {
          if(state_code) {
            switch(state_code) {
              case "PROCESSED-ON-DELIVERY":
                  state_code = "In carico al cassiere";
              break;
            }
          }
          return state_code;
        }
      }      
    });
});