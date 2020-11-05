<template>

	<div id="accordion-suppliers">
	
		<div v-if="isRunOrders" class="box-spinner"> 
			<div class="spinner-border text-info" role="status">
			  <span class="sr-only">Loading...</span>
			</div>	
		</div>

	    <div v-if="!isRunOrders" class="card" 
	          v-for="(order, index)  in orders"
	          :order="order"
	          :key="index"
		  >

			    <div class="card-header" data-toggle="collapse" :data-target="'#'+order.id" aria-expanded="true" :aria-controls="'collapse-'+order.id" v-on:click="selectOrder(order)">
	
					<img style="max-width:150px" v-if="order.suppliers_organization.supplier.img1 != ''" 
						class="img-supplier" 
						:src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
						:alt="order.suppliers_organization.name">

					{{ order.suppliers_organization.name }}
					{{ order.delivery.luogo }} 

		              <span v-if="order.order_state_code.code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
		              <span v-if="order.order_state_code.code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>
		              <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">Riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>

		              <i :id="'fas-'+order.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>
			    </div>

			    <div :id="'collapse-'+order.id" class="collapse" :aria-labelledby="'heading-'+order.id" data-parent="#accordion-suppliers">
				    <div class="card-body">

				        <div v-if="isRunOrders" class="box-spinner"> 
				        	<div class="spinner-border text-info" role="status">
					          <span class="sr-only">Loading...</span>
					        </div>	  
					    </div>

				        <div v-if="!isRunOrders">
							{{ order }}
					    </div>
				    </div>
			    </div>


	  	</div>

	</div> <!-- <div accordion-suppliers -->

</template>

<script>
import { mapActions } from "vuex";
import Order from '../components/Order.vue';
    
export default {
  name: "orders-suppliers",
  data() {
    return {
      suppliers: null,
      orders: {},
      isRunOrders: false,
      isRunOrders: false,
    };
  }, 
    props: {
        datas: {}
    },  
  watch: {
  	datas (newValue, oldValue) { 
  		this.orders = newValue;
  	}
  },     
  components: {
    Order
  }, 
  mounted() {
    //this.getSupplierOrganizations();
  },
  methods: { 
	  	...mapActions(["addOrder"]), 
	    getSupplierOrganizations() {

	      this.isRunOrders=true;

	      let url = "/admin/api/orders/gets";
	      axios
	        .post(url)
	        .then(response => {

				this.isRunOrders=false;

				console.log(response.data);
				if(typeof response.data !== "undefined") {
					this.orders = response.data;
					console.log(this.orders);
				}
	        })
	        .catch(error => {
	       	  this.isRunOrders=false;
	          console.error("Error: " + error);
	        });
	    },
	    selectSupplierOrganizations(delivery_id) {
	    	console.log('selectSupplierOrganizations '+delivery_id);

			let isOpen = $('#collapse-'+delivery_id).hasClass('show');
			
			$('.collapse').removeClass('show');
			$('#accordion-suppliers .fas').removeClass("fa-angle-up");
			$('#accordion-suppliers .fas').addClass("fa-angle-down");

			if(!isOpen) {
				$('#collapse-'+delivery_id).addClass('show');
				$('#accordion-suppliers #fas-'+delivery_id).addClass("fa-angle-up");
			}
			
			this.isRunOrders=true;
				
			let params = {
				delivery_id: delivery_id
			};

			this.orders = [];

			let url = "/admin/api/orders/gets";
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
	    	console.log('selectOrder ');
	    	console.log(order);


			let isOpen = $('#collapse-'+order.id).hasClass('show');
			
			$('.collapse').removeClass('show');
			$('#accordion-suppliers .fas').removeClass("fa-angle-up");
			$('#accordion-suppliers .fas').addClass("fa-angle-down");

			if(!isOpen) {
				$('#collapse-'+order.id).addClass('show');
				$('#accordion-suppliers #fas-'+order.id).addClass("fa-angle-up");
			}
			
			this.isRunOrders=true;
				
			this.order;
			this.isRunOrders=false;
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