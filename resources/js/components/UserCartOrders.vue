<template>

	<main>

        <div class="box-btn-pdf">
           <a :href="'/admin/api/exports/user-cart/'+results.delivery_id" target="_blank" title="Stampa carrello" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Stampa carrello della consegna</a>
        </div>

        <p 
          v-for="(order, index) in results.orders"
          :order="order"
          :key="order.id" class="box-order">

					<a v-on:click="selectOrder(order)" href="#" class="row-gray">

						<div class="content-img-supplier-small">
							<img v-if="order.suppliers_organization.supplier.img1 != ''" 
								class="img-supplier-small" 
								:src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
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
			
        </p> <!-- loop orders -->

				<!-- 		  		-->
				<!--  TOTALE  -->
				<!-- 		 		  -->
	      <div class="row">

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

				<!-- 		  		-->
				<!-- DISTANCE -->
				<!-- 		  		-->
				<p class="box-distance">
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

	</main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartArticles from "../components/part/UserCartArticles.vue";

export default {
  name: "user-cart-orders",
  /*
   * results: {
   * 		delivery_id: null,
   *   	orders: [],
   *  	promotions: []
   * },
   */
  props: {
    results: {}
  },
  data() {
    return {
      j_seo: '',
      organizationTemplatePayToDelivery: '',    
    };
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