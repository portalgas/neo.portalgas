<template>

	<main id="accordion-deliveries">

	<modal-article-orders-cart></modal-article-orders-cart>

    <div v-if="isRunDeliveries" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>	  
    </div>
		<div v-if="!isRunDeliveries && deliveries!==null && deliveries.length==0" class="alert alert-warning">
			Non ci sono consegne disponibili
		</div>

	    <div class="card" 
	          v-for="(delivery, index)  in deliveries"
	          :delivery="delivery"
	          :key="delivery.id"
			  v-if="deliveries!==null && deliveries.length>0" 
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
			
			<div v-if="!isRunOrders && orders!==null && orders.data!==null && orders.data.length===0" class="alert alert-warning">
			Non ci sono ordini associati
			</div>
			<div v-else>

				<div class="row">
					<div class="col col-1 div-th">
					</div>
					<div class="col col-4 div-th">
					Produttore
					</div>
					<div class="col col-2 div-th">
					Chiusura ordine
					</div>
					<div class="col col-2 div-th">
					Stato
					</div>
					<div class="col col-2 div-th">
					Frequenza
					</div>
					<div class="col col-1 div-th">
					</div>
				</div> <!-- row -->
				<div class="row" 
					v-for="(order, index) in orders.data" v-if="!isRunOrders && orders.delivery_id===delivery.id"
					:order="order"
					:key="order.id"
					:class="'order-row type-'+order.order_type.name">

					<div class="col col-1">
						<img style="max-width:50px" v-if="order.suppliers_organization.supplier.img1 != ''" 
							class="img-supplier-disabled" 
							:src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
							:alt="order.suppliers_organization.name">
					</div>
					<div class="col col-4">
						<a  v-if="order.order_state_code.code=='OPEN' || order.order_state_code.code=='RI-OPEN-VALIDATE'"
							v-on:click="selectOrder(order)" 
							href="#">{{ order.suppliers_organization.name }}</a>
						<span v-else>{{ order.suppliers_organization.name }}</span>

						<span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>
											
					</div>
					<div class="col col-2">
						{{ order.data_fine | formatDate }}
					</div>
					<div class="col col-2">
						<span v-if="missingDays(order.data_fine) < 0" style="color: red;">Chiuso</span>
						<span v-else>Mancano {{ missingDays(order.data_fine) }} giorni.</span>
					</div>
					<div class="col col-2">
						{{  order.suppliers_organization.frequenza  }}
					</div>
					<div class="col col-1">
						<a v-on:click="clickShowOrHiddenModalArticleOrdersCart(order)" href="#"><img class="img-responsive-disabled" src="/images/cake/cesta-piena.png" title="Prodotti ordinati" border="0"></a>
						<img class="img-responsive-disabled" src="/images/cake/cesta-vuota.png" title="Nessun prodotto ordinato" border="0">
					</div>
				</div> <!-- row --> 
			</div> <!-- all rows -->
	      </div>
	    </div>

	  </div>
	</main> 
</template>

<script>
import { mapActions } from "vuex";
import modalArticleOrdersCart from "../components/part/ModalArticleOrdersCart.vue";

export default {
  name: "deliveries",
  components: {
    modalArticleOrdersCart: modalArticleOrdersCart
  },
  data() {
    return {
      deliveries: null,
      isRunDeliveries: false,
      orders: {
      	id: null,
      	data: []
      },
      isRunOrders: false,
    };
  }, 
  mounted() {
    this.getDeliveries()
  },
  methods: { 
	...mapActions(['showModalArticleOrdersCart', 'showOrHiddenModalArticleOrdersCart', 'addModalContent', 'clearModalContent']),
    clickShowOrHiddenModalArticleOrdersCart (order) {

		this.clearModalContent();

		var modalContent = {
			title: 'test',
			body: 'body '+order.id,
			order: order,
			footer: '',
			msg: ''
		}            


		this.addModalContent(modalContent);
		this.showOrHiddenModalArticleOrdersCart();              
	},  
	getDeliveries() {
		let url = '/admin/api/deliveries/getAlls';	
		this.isRunDeliveries=true;
			axios
			.post(url)
			.then(response => {

			this.isRunDeliveries=false;

			// console.log(response.data);
			if(typeof response.data !== "undefined") {
				this.deliveries = response.data;        
			}
		})
		.catch(error => {
			this.isRunDeliveries=false;
			console.error("Error: " + error);
		});
	},
	selectDelivery(delivery_id) {
		/* console.log('selectDelivery '+delivery_id); */

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

		let url = "/admin/api/orders/getAlls";
		// console.log(url);
		axios
			.post(url, params)
			.then(response => {

				this.isRunOrders=false;
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
		let params = {}
		params = {order_type_id: order.order_type_id, order_id: order.id, is_social_market: false}
		this.$router.push({ name: 'Order', params: params})
	},
	missingDays(data_fine) {
		const dataTarget = new Date(data_fine);
		const oggi = new Date();
		const differenzaInMs = dataTarget - oggi;
		return Math.ceil(differenzaInMs / (1000 * 60 * 60 * 24));
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
.div-th {
  background-color: #0a659e;
  border-right: 1px solid #fff;
  font-size: 16px;
  font-weight: normal;
  color: #fff;
  padding: 10px;
  margin-bottom: 25px;
}
.order-row {
	border-bottom: 1px solid #c2c2c2;
	padding-top: 15px;
	padding-bottom: 15px;
}
</style> 