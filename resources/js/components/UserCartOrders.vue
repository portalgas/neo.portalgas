<template>

	<div id="accordion-deliveries">

	    <div class="card" 
	          v-for="(delivery, index)  in datas"
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

	        <div class="box-btn-pdf" v-if="!isRunOrders && results.delivery_id===delivery.id">
	           <a :href="'/admin/api/exports/user-cart/'+delivery.id" target="_blank" title="Stampa carrello" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Stampa carrello della consegna</a>
	        </div>

	        <p 
	          v-for="(order, index) in results.orders" v-if="!isRunOrders && results.delivery_id===delivery.id"
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
						<small v-if="order.suppliers_organization.supplier.descrizione!=''">{{ order.suppliers_organization.supplier.descrizione }}</small>

						<span class="d-none d-md-inline-block d-lg-inline-block d-xl-inline-block">
			              <span v-if="order.order_state_code.code=='OPEN-NEXT'">- aprirà {{ order.data_inizio | formatDate }} </span>
			              <span v-if="order.order_state_code.code=='OPEN'">- chiuderà {{ order.data_fine | formatDate }}</span>
			              <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">- data chiusura {{ order.data_fine | formatDate }}</span>
			              <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">- riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>
			            </span>

					    <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
					    <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span> 
					</a>

			        <user-cart-articles 
			        			:order="order" 
			        			:article_orders="order.article_orders"
			        			></user-cart-articles>
				
	        </p> 

			<!-- 		  -->
			<!--  TOTALE  -->
			<!-- 		  -->
		      <div v-if="!isRunOrders" class="row">

		      	<div class="footer col-sm-12 col-xs-12 col-md-12" 
		      		v-if="organizationTemplatePayToDelivery=='POST'">
					Totale presunto della consegna: {{ totalPrice() }} &euro;
					<br /><i>(il totale effettivo per effettuare i pagamenti dev'essere confermato dal tesoriere)</i>
		      	</div>
		      	<div class="footer col-sm-12 col-xs-12 col-md-12" 
		      		v-if="organizationTemplatePayToDelivery=='ON' || organizationTemplatePayToDelivery=='ON-POST'">
					Totale presunto della consegna: {{ totalPrice() }} &euro;
					<br /><i>(il totale effettivo per effettuare i pagamenti dev'essere confermato dal cassiere)</i>
		      	</div>
		      </div>

			<!-- 		  -->
			<!-- DISTANCE -->
			<!-- 		  -->
			<p v-if="!isRunOrders" class="box-distance">
				<h2>Quanta strada hanno fatto i tuoi acquisti?</h2>
		        <span 
		          v-for="(order, index) in results.orders" v-if="order.distance!=null"
		          :order="order"
		          :key="index">
		
						<div style="border-bottom:0px solid #fff;">{{ order.distance.supplierName }} da {{ order.distance.supplierLocalita }} ha percorso {{ order.distance.distance }} Km
						</div>
						<div class="progressBar" 
							:style="{width: order.distance.percentuale + '%'}">&nbsp;</td>	
							</div>
				</span>
				<div class="totaleKm">per un totale di {{ totalKm() }} Km</div>
			</p>

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
      j_seo: '',
      organizationTemplatePayToDelivery: '',    
      deliveries: null,
      results: {
      	delivery_id: null,
      	orders: []
      },
      isRunOrders: false
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
  mounted() {
    this.getGlobals();
  },   
  methods: { 
	    getGlobals() {
	      /*
	       * variabile che arriva da cake, dichiarata come variabile in Layout/vue.ctp, in app.js settata a window. 
	       * recuperata nei components con getGlobals()
	       */
	      this.j_seo = window.j_seo;
	      this.organizationTemplatePayToDelivery = window.organizationTemplatePayToDelivery;
	    }, 
  		totalKm() {
	    	var totale = 0;

	    	/* console.log("Totale ordini "+this.results.orders.length); */
	    	if(typeof this.results.orders !== "undefined" && this.results.orders.length>0) {

	    		this.results.orders.forEach(function (order, index) { 
	    			/ *console.log("Tratto ordine "+(index+1)); */

					if(order.distance!=null) {
						totale += order.distance.distance
					}

	    		}); /* loop orders */
	    	}

	    	totale = parseFloat(totale).toFixed(2);
	    	return totale;
  		},
	    totalPrice() {
	    	var totale = 0;

	    	console.log("Totale ordini "+this.results.orders.length); 
	    	if(typeof this.results.orders !== "undefined" && this.results.orders.length>0) {

	    		this.results.orders.forEach(function (order, index) { 
	    			
	    			// console.log("Tratto ordine "+(index+1)+' totale '+totale);
	    			// console.log(order);

	    			order.article_orders.forEach(function (article_order, index2) { 
	    				// console.log(article_order); 

	    				if(article_order.isOpenToPurchasable)  /* aperto per acquistare */
	    					totale += (article_order.cart.qta_new * article_order.price);
	    				else {
	    					/* ordine chiuso agli acquisti */
	    					totale = (totale + parseFloat(article_order.cart.final_price));	    					
	    				}

						// totale = parseFloat(totale).toFixed(2);
	    			}); /* loop article_orders */

					// totale = totale.replace(',', '.');

					// console.log('totalPrice() totale '+totale);

					if(order.trasport!=0.00 && order.summary_order_trasport!=null && order.summary_order_trasport.importo_trasport!=null)
						totale = (parseFloat(totale) + parseFloat(order.summary_order_trasport.importo_trasport));

					if(order.cost_more!=0.00 && order.summary_order_cost_more!=null && order.summary_order_cost_more.importo_cost_more!=null)
						totale = (parseFloat(totale) + parseFloat(order.summary_order_cost_more.importo_cost_more));

					if(order.cost_less!=0.00 && order.summary_order_cost_less!=null && order.summary_order_cost_less.importo_cost_less!=null)
						totale = (parseFloat(totale) + parseFloat(order.summary_order_cost_less.importo_cost_less));

					// totale = parseFloat(totale).toFixed(2);	 

					// console.log("Fine ordine "+(index+1)+' totale '+totale);   			
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
				// console.log('Tab chiuso => lo apro ');
				$('#collapse-'+delivery_id).addClass('show');
				$('#accordion-deliveries #fas-'+delivery_id).addClass("fa-angle-up");
			}
			else {
				// console.log('Tab aperto => esco ');
				return;
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

					/* console.log(response.data); */
					if(typeof response.data !== "undefined") {
						var data = {
							delivery_id: delivery_id,
							orders: response.data
						}
						this.results = data;
						// console.log(this.results);
				}
			})
			.catch(error => {

				this.isRunOrders=false;

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
	    selectOrder(order) {
	    	console.log('selectOrder');
	    	console.log(order);
	    	
	    	this.$router.push({ name: 'Order', params: {order_type_id: order.order_type_id, order_id: order.id}})
	    }    
  	},
	filters: {
    	currency(amount) {
	      let locale = window.navigator.userLanguage || window.navigator.language;
          locale = 'it-IT';
	      const amt = Number(amount);
	      return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
	    },
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            locale = 'it-IT';
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
	background-color: #e4e4e4;
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
.box-distance {
    padding-top: 5px;
}
.totaleKm {
    font-size: 18px;
    padding: 0;
    text-align: right;
}
.progressBar {
	background-color: #0a659e;
}
</style> 