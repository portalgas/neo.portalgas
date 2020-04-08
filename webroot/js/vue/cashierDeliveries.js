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
    var htmlResultOrders = $('.result-orders');
    var htmlResultUsers = $('.result-users');

    vueCashiers = new Vue({
      router,
      el: '#vue-cashiers',
      data: {
        errors: [],
        orders: null,
        users: null,
        is_cash: '1', 
        is_found: false   
      },  
      methods: {
        setIsCash: function(e) {

        },
        getOrdersByDelivery: function(e) {

            let orders_state_code = 'PROCESSED-ON-DELIVERY';
            let delivery_id = $("select[name='delivery_id']").val();
            console.log('delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.result-orders').hide();
                htmlResultOrders.hide();
                this.is_found = false;
                $('#submit').addClass('disabled');
                return;
            }

            $('.result-orders').show();
            htmlResultOrders.show();
            htmlResultOrders.addClass('fa fa-spinner');

            let params = {
                delivery_id: delivery_id,
                orders_state_code: orders_state_code
            }; 

            http.post(ajaxUrlGetOrdersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  htmlResultOrders.removeClass('fa-lg fa fa-spinner');
                  this.is_found = true;
                  this.orders = response.data;        
                  $('#submit').removeClass('disabled');
                })
            .catch(error => {
                  htmlResultOrders.removeClass('fa-lg fa fa-spinner');
                  this.is_found = false;
                  console.log("Error: " + error);
            });            
        },
        getCompleteUsersByDelivery: function(e) {

            let delivery_id = $("select[name='delivery_id']").val();
            console.log('delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.result-users').hide();
                htmlResultUsers.hide();
                this.is_found = false;   
                $('#submit').addClass('disabled');
                return;
            }

            $('.result-users').show();
            htmlResultUsers.show();
            htmlResultUsers.addClass('fa fa-spinner');

            let params = {
                delivery_id: delivery_id
            }; 

            http.post(ajaxUrlGetCompleteUsersByDelivery, params)
                .then(response => {
                  /* console.log(response.data); */
                  htmlResultUsers.removeClass('fa-lg fa fa-spinner');
                  this.is_found = true;
                  this.users = response.data;
                  $('#submit').removeClass('disabled');
                })
            .catch(error => {
                 htmlResultUsers.removeClass('fa-lg fa fa-spinner');
                 this.is_found = false;
                 console.log("Error: " + error);
            });            
        }        
      },
      mounted: function(){
        console.log('mounted vueCasheirs');
      },
      filters: {
        currency(amount) {
          const amt = Number(amount);
          return amt && amt.toLocaleString('it-IT', {maximumFractionDigits:2}) || '0'
        }
      }      
    });
}