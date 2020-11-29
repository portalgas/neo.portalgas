<template>

	<div id="accordion-deliveries">

	    <div class="card" 
	          v-for="(delivery, delivery_id)  in deliveries"
	          :delivery="delivery"
	          :key="delivery_id"
		  >
	    <div class="card-header" data-toggle="collapse" :data-target="'#'+delivery_id" aria-expanded="true" :aria-controls="'collapse-'+delivery_id" v-on:click="selectDelivery(delivery_id)">
	          {{ delivery }}
              <i :id="'fas-'+delivery_id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
	    </div>

	    <div :id="'collapse-'+delivery_id" class="collapse" :aria-labelledby="'heading-'+delivery_id" data-parent="#accordion-deliveries">
	      <div class="card-body">

	        <div v-if="isRunOrders" class="box-spinner"> 
	        	<div class="spinner-border text-info" role="status">
		          <span class="sr-only">Loading...</span>
		        </div>	  
		    </div>

	        <p 
	          v-for="(order, index) in orders.data" v-if="!isRunOrders && orders.delivery_id===delivery_id"
	          :order="order"
	          :key="order.id">

					<a v-on:click="selectOrder(order)" href="#" class="row-gray">

						<div class="content-img-supplier-small">
							<img v-if="order.suppliers_organization.supplier.img1 != ''" 
								class="img-supplier-small" 
								:src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
								:alt="order.suppliers_organization.name">
						</div>

						{{ order.suppliers_organization.name }}

					    <span>{{ order.data_inizio | formatDate }} - {{ order.data_fine | formatDate }}</span>

					    <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
					    <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span> 
					</a>

			        <user-cart-articles :order="order" :article_orders="order.article_orders"></user-cart-articles>
				
	        </p> 

		      <div class="row">
		        <div class="footer col-sm-12 col-xs-12 col-md-12">Totale carrello per la consegna : {{ totalPrice() }} &euro;</div>
		      </div>

	      </div>
	    </div>

	  </div>
	</div> <!-- <div accordion-deliveries -->

</template>

<script>
import { mapActions } from "vuex";
import UserCartArticles from "../components/part/UserCartArticles.vue";

export default {
  name: "user-cart-deliveries",
  data() {
    return {
      deliveries: null,
      orders: {
      	id: null,
      	data: []
      },
      isRunOrders: false,
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
    UserCartArticles
  }, 
  computed: {
	getArticleOrders: function(q) {
      return this.order.article_order
    },  
  },
  methods: {
	    totalPrice() {
	    	/* console.log("Totale ordini "+this.orders.data.length); */
	    	if(typeof this.orders.data !== "undefined" && this.orders.data.length>0) {
	    		var totale = 0;
	    		this.orders.data.forEach(function (order, index) { 
	    			/ *console.log("Tratto ordine "+(index+1)); */
	    			order.article_orders.forEach(function (article_order, index) { 
	    				/* console.log(article_order);  */
	    				totale += (article_order.cart.qta_new * article_order.price);
	    			});
	    		});
	    	}

	    	return this.$options.filters.currency(totale);
	    },
	    selectDelivery(delivery_id) {
	    	console.log('selectDelivery '+delivery_id);

			let isOpen = $('#collapse-'+delivery_id).hasClass('show');
			
			$('.collapse').removeClass('show');
			$('#accordion-deliveries .fas').removeClass("fa-angle-up");
			$('#accordion-deliveries .fas').addClass("fa-angle-down");

			if(!isOpen) {
				$('#collapse-'+delivery_id).addClass('show');
				$('#accordion-deliveries #fas-'+delivery_id).addClass("fa-angle-up");
			}
			
			this.isRunOrders=true;
				
			let params = {
				delivery_id: delivery_id
			};

			this.orders = [];

			let url = "/admin/api/orders/user-cart-gets";
			axios
				.post(url, params)
				.then(response => {

					this.isRunOrders=false;

					console.log(response.data);
					if(typeof response.data !== "undefined") {
						var data = {
							delivery_id: delivery_id,
							data: response.data
						}
						this.orders = data;
						console.log(this.orders);
				}
			})
			.catch(error => {

				this.isRunOrders=false;

				console.error("Error: " + error);
			});
	    },
	    selectOrder(order) {
	    	console.log('selectOrder');
	    	console.log(order);
	    	
	    	this.$router.push({ name: 'Order', params: {order_type_id: order.order_type_id, order_id: order.id}})
	    }    
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
          counter: function (index) {
            return index+1
        }
     }
};
</script>

<style scoped>
.row-gray {
    background-color: #dee2e6;
    display: block;
    padding: 10px;
}
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
	background-color: #bababa;
	font-weight: bold;
	color: #0a659e;
	text-align: right; 
}
</style> 