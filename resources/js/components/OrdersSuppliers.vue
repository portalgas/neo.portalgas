<template>

	<main id="accordion-suppliers">
	
		<div v-if="!dataNotFound" class="alert alert-warning">
			Non ci sono ordini associati
		</div>

	    <div class="card" 
	          v-for="(order, index)  in orders"
	          :order="order"
	          :key="index"
	          :class="'type-'+order.order_type.name"
		  >

			    <div class="card-header" data-toggle="collapse" :data-target="'#'+order.id" aria-expanded="true" :aria-controls="'collapse-'+order.id" v-on:click="selectOrder(order)">
	
					<div class="content-img-supplier">
						<img v-if="order.suppliers_organization.supplier.img1 != ''" 
							class="img-supplier" 
							:src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
							:alt="order.suppliers_organization.name">
					</div>
					
					{{ order.suppliers_organization.name }}&nbsp;

                <span v-if="!is_social_market && order.delivery.sys=='Y'">
                    {{ order.delivery.luogo }}
                </span>
                <span v-if="!is_social_market && order.delivery.sys!='Y'">
                    {{ order.delivery.luogo }} il {{ order.delivery.data | formatDate }}
                </span>


					      <span class="d-none d-md-inline-block d-lg-inline-block d-xl-inline-block" v-if="!is_social_market">
		              <span v-if="order.order_state_code.code=='OPEN-NEXT'">- aprirà {{ order.data_inizio | formatDate }} </span>
		              <span v-if="order.order_state_code.code=='OPEN'">- chiuderà {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">- data chiusura {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">- riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>
		            </span>

					    <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
					    <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>  

					     <div v-if="order.nota!=null && order.nota!=''" class="col-10 alert alert-info ml-auto mr-1" 
					     	v-html="$options.filters.html(order.nota)">
					     </div>

		                <i :id="'fas-'+order.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
			    </div>

			    <div :id="'collapse-'+order.id" class="collapse" :aria-labelledby="'heading-'+order.id" data-parent="#accordion-suppliers">
				    <div class="card-body">

				    </div>
			    </div>


	  	</div>

	</main> 
	
</template>

<script>
import { mapActions } from "vuex";
import Order from '../views/Order.vue';
    
export default {
  name: "orders-suppliers",
  components: {
    Order
  },   
  data() {
    return {
      suppliers: null,
      orders: {},
      isRunOrders: false,
    };
  }, 
  /*
   * in Tabs al click isLoading=true e Tab popola datas con chiamata ajax
   */  
  props: {
    datas: {},
    dataNotFound: true,
    is_social_market: false /* se true e' SocialMarket */
  },  
  watch: {
  	datas (newValue, oldValue) { 
  		this.orders = newValue;
  	}
  },     
  methods: { 
	    selectOrder(order) {
        /* console.log('selectOrder'); */
        /* console.log(order); */

        let isOpen = $('#collapse-'+order.id).hasClass('show');

        $('.collapse').removeClass('show');
        $('#accordion-suppliers .fas').removeClass("fa-angle-up");
        $('#accordion-suppliers .fas').addClass("fa-angle-down");

        if(!isOpen) {
          $('#collapse-'+order.id).addClass('show');
          $('#accordion-suppliers #fas-'+order.id).addClass("fa-angle-up");
        }


        let params = {}
        if(this.is_social_market)
          params = {order_type_id: order.order_type_id, order_id: order.id, is_social_market: 'socialmarket'}
        else
          params = {order_type_id: order.order_type_id, order_id: order.id, is_social_market: 'socialmarket'}
        this.$router.push({ name: 'Order', params: params})
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
	        },
		    html(text) {
		        return text;
		    },
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
.type-PROMOTION {
  background-image: url("/img/promotion-75w-82h.png");
  background-repeat: no-repeat, no-repeat;
  background-position: right center;
}
@media screen and (max-width: 600px) {
  .type-PROMOTION {
     background-image: none;
  }
}
</style> 