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
	        <p 
	          v-for="(order, index)  in orders.data" v-if="orders.delivery_id===delivery_id"
	          :order="order"
	          :key="order.id">
					<a v-bind:href="'/order/'+order.id">

						<img v-if="order.suppliers_organization.supplier.img1 != ''" 
							class="img-fluid img-thumbnail" 
							:src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
							:alt="order.suppliers_organization.name">

						{{ order.suppliers_organization.name }}

					    <span>{{ order.data_inizio | formatDate }} - {{ order.data_fine | formatDate }}</span>

					    {{ order.state_code }}  
					</a>
	        </p> 
	      </div>
	    </div>

	  </div>
	</div> <!-- <div accordion-deliveries -->

</template>

<script>
export default {
  name: "app-deliveries",
  data() {
    return {
      deliveries: null,
      orders: {
      	id: null,
      	data: []
      },
    };
  },
  mounted() {
    	this.gets();
  },
  methods: {
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
		
				
			let params = {
				delivery_id: delivery_id
			};

			this.orders = [];

			let url = "/admin/api/orders/gets";
			axios
				.post(url, params)
				.then(response => {
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
				console.error("Error: " + error);
			});
	    },  
	    gets() {
	      let url = "/admin/api/deliveries/gets";
	      axios
	        .post(url)
	        .then(response => {
	          console.log(response.data);
	          if(typeof response.data !== "undefined") {
	            this.deliveries = response.data;
	            console.log(this.deliveries);
	          }
	        })
	        .catch(error => {
	          console.error("Error: " + error);
	        });
	    },
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