<template>

	<div id="accordion-deliveries">

		<div v-if="!dataNotFound" class="alert alert-warning">
			Non ci sono ordini associati
		</div>

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

	        <p 
	          v-for="(order, index)  in orders.data" v-if="!isRunOrders && orders.delivery_id===delivery.id"
	          :order="order"
	          :key="order.id">
					<a v-on:click="selectOrder(order)" href="#">

						<div class="content-img-supplier">
							<img style="max-width:150px" v-if="order.suppliers_organization.supplier.img1 != ''" 
								class="img-supplier" 
								:src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
								:alt="order.suppliers_organization.name">
						</div>

						{{ order.suppliers_organization.name }}

					    <span>{{ order.data_inizio | formatDate }} - {{ order.data_fine | formatDate }}</span>

					    <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
					    <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>  
					</a>
	        </p> 
	      </div>
	    </div>

	  </div>
	</div> <!-- <div accordion-deliveries -->

</template>

<script>
import { mapActions } from "vuex";

export default {
  name: "orders-deliveries",
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
    datas: {},
    dataNotFound: true
  },
  watch: {
  	datas (newValue, oldValue) { 
  		this.deliveries = newValue;
  	}
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

			this.isRunOrders=true;
				
			let params = {
				delivery_id: delivery_id
			};

			this.orders = [];

			let url = "/admin/api/orders/gets";
			// console.log(url);
			axios
				.post(url, params)
				.then(response => {

					this.isRunOrders=false;

					// console.log(response.data);
					if(typeof response.data !== "undefined") {
						var data = {
							delivery_id: delivery_id,
							data: response.data
						}
						this.orders = data;
						// console.log(this.orders);
				}
			})
			.catch(error => {

				this.isRunOrders=false;

				console.error("Error: " + error);
			});
	    },
	    selectOrder(order) {
	    	// console.log('selectOrder');
	    	// console.log(order);
	    	
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