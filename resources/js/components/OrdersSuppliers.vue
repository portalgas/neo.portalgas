<template>

	<div id="accordion-suppliers">
	

	    <div class="card" 
	          v-for="(order, index)  in orders"
	          :order="order"
	          :key="index"
		  >

			    <div class="card-header" data-toggle="collapse" :data-target="'#'+order.id" aria-expanded="true" :aria-controls="'collapse-'+order.id" v-on:click="selectOrder(order)">
	
					<div class="content-img-supplier">
						<img v-if="order.suppliers_organization.supplier.img1 != ''" 
							class="img-supplier" 
							:src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
							:alt="order.suppliers_organization.name">
					</div>
					
					{{ order.suppliers_organization.name }}
					{{ order.delivery.luogo }} 

		              <span v-if="order.order_state_code.code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
		              <span v-if="order.order_state_code.code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">Riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>

					    <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
					    <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>  

		              <i :id="'fas-'+order.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
			    </div>

			    <div :id="'collapse-'+order.id" class="collapse" :aria-labelledby="'heading-'+order.id" data-parent="#accordion-suppliers">
				    <div class="card-body">

				    </div>
			    </div>


	  	</div>

	</div> <!-- <div accordion-suppliers -->

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
        datas: {}
  },  
  watch: {
  	datas (newValue, oldValue) { 
  		this.orders = newValue;
  	}
  },     
  methods: { 
	    selectOrder(order) {
	    	console.log('selectOrder');
	    	console.log(order);

			let isOpen = $('#collapse-'+order.id).hasClass('show');
			
			$('.collapse').removeClass('show');
			$('#accordion-suppliers .fas').removeClass("fa-angle-up");
			$('#accordion-suppliers .fas').addClass("fa-angle-down");

			if(!isOpen) {
				$('#collapse-'+order.id).addClass('show');
				$('#accordion-suppliers #fas-'+order.id).addClass("fa-angle-up");
			}
			
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
</style> 