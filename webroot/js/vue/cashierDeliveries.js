"use strict";

var vueCashiers = null;

window.onload = function () {
    
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
            console.log(ajaxUrlGetOrdersByDelivery+' delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.run-orders .spinner').removeClass(ico_spinner);
                //$('#submit').addClass('disabled');
                return;
            }

            $('.run-orders').show();
            $('.run-orders .spinner').addClass(ico_spinner);

            let params = {
                delivery_id: delivery_id,
                orders_state_code: orders_state_code
            }; 

            axios.post(ajaxUrlGetOrdersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  $('.run-orders .spinner').removeClass(ico_spinner);
                  this.is_found_orders = true;
                  this.orders = response.data;        
                  // $('#submit').removeClass('disabled');
                })
            .catch(error => {
                  $('.run-orders .spinner').removeClass(ico_spinner);
                  this.is_found_orders = false;
                  console.log("Error: " + error);
            });            
        },
        getCompleteUsersByDelivery: function(e) {

            this.is_found_users = false;

            let delivery_id = $("select[name='delivery_id']").val();
            console.log(ajaxUrlGetCompleteUsersByDelivery+' delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.run-users .spinner').removeClass(ico_spinner); 
                $('#submit').addClass('disabled');
                return;
            }

            $('.run-users').show();
            $('.run-users .spinner').addClass(ico_spinner);

            let params = {
                delivery_id: delivery_id
            }; 

            axios.post(ajaxUrlGetCompleteUsersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  $('.run-users .spinner').removeClass(ico_spinner);
                  this.is_found_users = true;
                  this.users = response.data;
                  $('#submit').removeClass('disabled');
                })
            .catch(error => {
                 $('.run-users .spinner').removeClass(ico_spinner);
                 this.is_found_users = false;
                 console.log("Error: " + error);
            });            
        }        
      },
      mounted: function(){
        console.log('mounted vueCasheirs');
      },
      filters: {
        currency(amount) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          const amt = Number(amount);
          return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
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
}