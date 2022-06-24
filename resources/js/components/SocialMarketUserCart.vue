<template>

	<main>
        <user-cart-orders v-if="!isRun" :results="results"></user-cart-orders>
	</main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartOrders from '../components/UserCartOrders.vue';

export default {
  name: "social-market-user-cart",
  data() {
    return { 
      deliveries: null,
      results: {
      	delivery_id: null,
      	orders: [],
      	promotions: []
      },
      isRun: false
    };
  },
  components: {
    UserCartOrders
  },
  mounted() {
    this.selectDelivery();
  },
  methods: {
	    selectDelivery() {

        let delivery_id = 353;

				this.isRun=true;
					
				let params = {
					delivery_id: delivery_id
				};

				this.orders = [];

				let url_orders = "/admin/api/orders/user-cart-gets/public";

				axios
					.post(url_orders, params)
					.then(response => {

						this.isRun=false;

						/* console.log(response.data); */
						if(typeof response.data !== "undefined") {
							var data = {
								delivery_id: delivery_id,
								orders: response.data,
								promotions: []
							}
							this.results = data;
							// console.log(this.results);
					}
				})
				.catch(error => {

					this.isRun=false;

					console.error("Error: " + error);
				});
	    },
	    selectOrder(order) {
	    	console.log('selectOrder');
	    	console.log(order);
	    	
	    	this.$router.push({ name: 'Order', params: {order_type_id: order.order_type_id, order_id: order.id}})
	    }    
  	}
};
</script>

<style scoped>
.card { 
  border: none;
}
.card-header {
	cursor: pointer;
	color: #0a659e;
	font-weight: normal;
}
.card-header:hover {
	color: #fa824f;
}
.footer {
	background-color: #e4e4e4;
	font-weight: bold;
	color: #0a659e;
	text-align: right; 
}
.type-PROMOTION {
    float: right;
    background-image: url(/img/promotion-50w-55h.png);
    background-repeat: no-repeat, no-repeat;
}
.type-PROMOTION {
    background-position: 90% center;
}
@media (min-width: 800px) {
	.type-PROMOTION {
	    background-position: 96% center;
	}	
}
@media (min-width: 1500px) {
	.type-PROMOTION {
	    background-position: 98% center;
	}	
}

</style> 