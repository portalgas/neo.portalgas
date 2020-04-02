"use strict";

var vueCashiers = null;

window.onload = function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    var ajaxUrlGetOrdersByDelivery = '/admin/api/orders/getByDelivery';
    var ajaxUrlGetUsersByDelivery = '/admin/api/carts/getUsersByDelivery';
    var htmlResultOrders = $('.result-orders');
    var htmlResultUsers = $('.result-users');

    vueCashiers = new Vue({
      router,
      el: '#vue-cashiers',
      data: {
        errors: [],
        orders: null
      },  
      methods: {
        getOrdersByDelivery: function(e) {

            let orders_state_code = 'PROCESSED-ON-DELIVERY';
            let delivery_id = $("select[name='delivery_id']").val();
            console.log('delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.result-orders').hide();
                htmlResultOrders.hide();
                return;
            }

            $('.result-orders').show();
            htmlResultOrders.show();
            htmlResultOrders.addClass('fa fa-spinner');

            let params = {
                delivery_id: delivery_id,
                orders_state_code: orders_state_code
            }; 
            console.log(params); 

            http.post(ajaxUrlGetOrdersByDelivery, params)
                .then(response => {
                  console.log(response.data);
                  htmlResultOrders.removeClass('fa-lg fa fa-spinner');
                  this.orders = response.data;
                })
            .catch(error => {
                  htmlResultOrders.removeClass('fa-lg fa fa-spinner');
                  console.log("Error: " + error);
            });            
        },
        getUsersByDelivery: function(e) {

            let delivery_id = $("select[name='delivery_id']").val();
            console.log('delivery_id '+delivery_id);

            if(delivery_id==0 || delivery_id=='') {
                $('.result-users').hide();
                htmlResultUsers.hide();
                return;
            }

            $('.result-users').show();
            htmlResultUsers.show();
            htmlResultUsers.addClass('fa fa-spinner');

            let params = {
                delivery_id: delivery_id
            }; 
            console.log(params); 

            http.post(ajaxUrlGetUsersByDelivery, params)
                .then(response => {
                  console.log(response.data);
                  htmlResultUsers.removeClass('fa-lg fa fa-spinner');
                  this.users = response.data;
                })
            .catch(error => {
                 htmlResultUsers.removeClass('fa-lg fa fa-spinner');
                 console.log("Error: " + error);
            });            
        }        
      },
      mounted: function(){
        console.log('mounted vueCasheirs');
      }
    });
}