<template>

	<div id="accordion-deliveries">

	    <div class="card" 
	          v-for="(delivery, index)  in deliveries"
	          :delivery="delivery"
	          :key="delivery.id"
		  >
	    <div class="card-header" data-toggle="collapse" :data-target="'#'+delivery.id" aria-expanded="true" :aria-controls="'collapse-'+delivery.id" v-on:click="selectDelivery(delivery.id)">
	          {{ delivery.label }}
              <i :id="'fas-'+delivery.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
	    </div>

	    <div :id="'collapse-'+delivery.id" class="collapse" :aria-labelledby="'heading-'+delivery.id" data-parent="#accordion-deliveries">
	      <div class="card-body">

	        <div v-if="isRunOrders" class="box-spinner"> 
	        	<div class="spinner-border text-info" role="status">
		          <span class="sr-only">Loading...</span>
		        </div>	  
		    </div>

	        <div class="box-btn-pdf" v-if="!isRunOrders && orders.delivery_id===delivery.id">
	           <a :href="'/admin/api/exports/pdf/'+delivery.id" target="_blank" title="Stampa carrello" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Stampa carrello della consegna</a>
	        </div>

	        <p 
	          v-for="(order, index) in orders.data" v-if="!isRunOrders && orders.delivery_id===delivery.id"
	          :order="order"
	          :key="order.id" class="box-order">

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
      storerooms: {},
      isRunOrders: false,
      isRunStorerooms: false,
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
	    	var totale = 0;

	    	/* console.log("Totale ordini "+this.orders.data.length); */
	    	if(typeof this.orders.data !== "undefined" && this.orders.data.length>0) {
	    		var totale = 0;
	    		this.orders.data.forEach(function (order, index) { 
	    			/ *console.log("Tratto ordine "+(index+1)); */
	    			order.article_orders.forEach(function (article_order, index) { 
	    				/* console.log(article_order);  */

	    				if(order.isOpenToPurchasable) 
	    					totale += (article_order.cart.qta_new * article_order.price);
	    				else {
	    					/* ordine chiuso agli acquisti */
	    					totale += article_order.cart.final_price;
	    				}
	    				
	    			}); /* loop article_orders */

					// totale = totale.replace(',', '.');

					// console.log('totalPrice) totale '+totale);

					if(order.summary_order_trasport!=null)
					totale = (parseFloat(totale) + parseFloat(order.summary_order_trasport.importo_trasport));

					if(order.summary_order_cost_more!=null)
					totale = (parseFloat(totale) + parseFloat(order.summary_order_cost_more.importo_cost_more));

					if(order.summary_order_cost_less!=null)
					totale = (parseFloat(totale) + parseFloat(order.summary_order_cost_less.importo_cost_less));

					// console.log('totalPrice) totale '+parseFloat(totale));

					// totale = parseFloat(totale).toFixed(2);	    			
	    		}); /* loop orders */
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

			let url_orders = "/admin/api/orders/user-cart-gets";
			axios
				.post(url_orders, params)
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

			/*
			 * storerooms
			 */
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

.box-btn-pdf {
	text-align: right;
	width: 100%;
	margin-bottom: 5px;
}
.box-btn-pdf a {
	font-size: 22px;
}
.box-order {
	clear: both;
}
</style> 