<template>

	<main id="accordion-deliveries">

	    <div class="card" 
	          v-for="(delivery, index)  in datas"
	          :delivery="delivery"
	          :key="delivery.id"
		  >

		  <!-- ORDERS -->
	    <div v-if="delivery.id>0" class="card-header" data-toggle="collapse" :data-target="'#'+delivery.id" aria-expanded="true" :aria-controls="'collapse-'+delivery.id" v-on:click="selectDelivery(delivery.id)">
	          {{ delivery.label }}
              <i :id="'fas-'+delivery.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
	    </div>

		  <!-- PROMOTIONS -->
	    <div v-if="delivery.id==0" class="card-header type-PROMOTION" data-toggle="collapse" :data-target="'#'+delivery.id" aria-expanded="true" :aria-controls="'collapse-'+delivery.id" v-on:click="selectPromotion(delivery.id)">
	          {{ delivery.label }}
              <i :id="'fas-'+delivery.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
	    </div>


	    <div :id="'collapse-'+delivery.id" class="collapse" :aria-labelledby="'heading-'+delivery.id" data-parent="#accordion-deliveries">
	      <div class="card-body">

	        <div v-if="isRun" class="box-spinner"> 
		        	<div class="spinner-border text-info" role="status">
			          <span class="sr-only">Loading...</span>
			        </div>	  
		    	</div>

		      <!-- ORDERS -->
			    <user-cart-orders v-if="!isRun && results.delivery_id===delivery.id && delivery.id>0" :results="results"></user-cart-orders> 

					<!-- PROMOTIONS -->
					<user-cart-promotions v-if="!isRun && results.delivery_id===delivery.id && delivery.id==0" :results="results"></user-cart-promotions> 

	      </div> <!-- card-body -->
	    </div>

	  </div>
	</main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartOrders from '../components/UserCartOrders.vue';
import UserCartPromotions from '../components/UserCartPromotions.vue';

export default {
  name: "user-cart-deliveries",
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
  /*
   * in Tabs al click isLoading=true e Tab popola datas con chiamata ajax
   */
  props: {
    datas: {}
  },
  watch: {
  	datas (newValue, oldValue) { 
  		this.deliveries = newValue;
  	}
  },
  components: {
    UserCartOrders,
    UserCartPromotions
  }, 
  methods: {
	    selectDelivery(delivery_id) {
	    	console.log('selectDelivery '+delivery_id);

				let isOpen = $('#collapse-'+delivery_id).hasClass('show');
				
				$('.collapse').removeClass('show');
				$('#accordion-deliveries .fas').removeClass("fa-angle-up");
				$('#accordion-deliveries .fas').addClass("fa-angle-down");

				if(!isOpen) {
					// console.log('Tab chiuso => lo apro ');
					$('#collapse-'+delivery_id).addClass('show');
					$('#accordion-deliveries #fas-'+delivery_id).addClass("fa-angle-up");
				}
				else {
					// console.log('Tab aperto => esco ');
					return;
				}

				this.isRun=true;
					
				let params = {
					delivery_id: delivery_id
				};

				this.orders = [];

				let url_orders = "/admin/api/orders/user-cart-gets";
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

				/*
				 * storerooms
				 * lo visualizzo solo per pdf del carrello
				this.isRunStorerooms=true;
					
				this.storerooms = [];

				let url_storeroom = "/admin/api/storerooms/user-cart-gets";
				axios
					.post(url_storeroom, params)
					.then(response => {

						this.isRunStorerooms=false;

						console.log(response.data);
						if(typeof response.data !== "undefined") {
							this.storerooms = response.data;
							console.log(this.storerooms);
					}
				})
				.catch(error => {

					this.isRunStorerooms=false;

					console.error("Error: " + error);
				});
				*/
	    },
	    selectPromotion(delivery_id) {
	    	console.log('selectPromotion '+delivery_id);

				let isOpen = $('#collapse-'+delivery_id).hasClass('show');
				
				$('.collapse').removeClass('show');
				$('#accordion-deliveries .fas').removeClass("fa-angle-up");
				$('#accordion-deliveries .fas').addClass("fa-angle-down");

				if(!isOpen) {
					// console.log('Tab chiuso => lo apro ');
					$('#collapse-'+delivery_id).addClass('show');
					$('#accordion-deliveries #fas-'+delivery_id).addClass("fa-angle-up");
				}
				else {
					// console.log('Tab aperto => esco ');
					return;
				}

				this.isRun=true;
				
				this.promotions = [];

				let url = "/admin/api/promotions/user-cart-gets";
				axios
					.post(url)
					.then(response => {

						this.isRun=false;

						 console.log(response.data); 
						if(typeof response.data !== "undefined") {
							var data = {
							  delivery_id: delivery_id,
							  orders: [],
								promotions: response.data.results
							}
							this.results = data;
							/* console.log(this.results); */
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