<template>

	<main id="accordion-deliveries">

	<modal-article-orders-cart></modal-article-orders-cart>

	<div v-if="isLoading" class="box-spinner">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-else>
            <section v-if="organization!=null">	
				<div class="row">
					<div class="col-md-12">
						<h2>
							<span style="float:left;">Consegne</span>
							<span style="float:right;">
								<Organizations :slug="slugGas" :organization="organization" />
							</span>
						</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
                        <Menu :slugGas="slugGas"></Menu>
                    </div>					
					<div class="col-md-10">

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
								
								<div v-if="!isRunOrders && results!==null && results.datas!==null && results.datas.length===0" class="alert alert-warning">
								Non ci sono ordini associati
								</div>
								<div v-else>
									
									<div class="row">
										<div class="col col-1 div-th">
										</div>
										<div class="col col-md-4 div-th">
										Produttore
										</div>
										<div class="col col-2 div-th">
										Chiusura ordine
										</div>
										<div class="col col-1 div-th d-none d-md-table-cell">
										Stato
										</div>
										<div class="col col-1 div-th d-none d-md-table-cell">
										Frequenza
										</div>
										<div class="col col-2 div-th d-none d-md-table-cell" v-if="results.user!==null">
										Referenti
										</div>
										<div class="col col-1 div-th d-none" v-if="results.user!==null">
										Acquisti
										</div>
										<div class="col col-3 div-th d-none" v-if="results.user==null"></div>
									</div> <!-- row -->
									<div class="row" 
										v-for="(order, index) in results.datas" v-if="!isRunOrders && results.delivery_id===delivery.id"
										:order="order"
										:key="order.id"
										:class="'order-row type-'+order.order.order_type.name">
				
										<div class="col col-1">
											<img style="max-width:50px" v-if="order.order.suppliers_organization.supplier.img1 != ''" 
												class="img-supplier-disabled" 
												:src="appConfig.$siteUrl+'/images/organizations/contents/'+order.order.suppliers_organization.supplier.img1"
												:alt="order.order.suppliers_organization.name">
										</div>
										<div class="col col-md-4">
											<a  v-if="order.user!==null && order.user.organization_id==organization.id &&(order.order.order_state_code.code=='OPEN' || order.order.order_state_code.code=='RI-OPEN-VALIDATE')"
												v-on:click="selectOrder(order.order)" 
												href="#">{{ order.order.suppliers_organization.name }}</a>
											<span v-else>{{ order.order.suppliers_organization.name }}</span>

											<span v-if="order.order.suppliers_organization.supplier.slug!='' && order.order.suppliers_organization.supplier.slug!=null">
												<a :href="'/site/produttore/'+order.order.suppliers_organization.supplier.slug" title="link alla pagina del produttore"><i class="fas fa-link"></i></a>
											</span>
											
											<span v-if="order.order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order.order_type.descri }}</span>
											
											<div v-if="order.order.nota!=''" class="alert alert-info">{{ order.order.nota }}</div>
										</div>
										<div class="col col-2">
											{{ order.order.data_fine | formatDate }}
										</div>
										<div class="col col-1 d-none d-md-table-cell">
											<span v-if="missingDays(order.order.data_fine) < 0" style="color: red;">Chiuso</span>
											<span v-else>Mancano {{ missingDays(order.order.data_fine) }} giorni.</span>
										</div>
										<div class="col col-1 d-none d-md-table-cell">
											{{  order.order.suppliers_organization.frequenza  }}
										</div>
										<div class="col col-2 d-none d-md-table-cell" v-if="results.user!==null">
											<referents v-if="order.user!==null && order.user.organization_id==organization.id && order.order.suppliers_organization.suppliers_organizations_referents!=null"
											:referents="order.order.suppliers_organization.suppliers_organizations_referents" 
											:email_visible=false />
										</div>
										<div class="col col-1" v-if="results.user!==null">
											<span v-if="order.user!==null && order.user.organization_id==organization.id">
												<span v-if="order.articles_orders.length>0">
													<a v-on:click="clickShowOrHiddenModalArticleOrdersCart(order)" 
														:title="'acquitati '+order.articles_orders.length+' articoli'"
														href="#"><img class="img-responsive-disabled" src="/images/cake/cesta-piena.png" border="0"></a>

														<p>Totale {{ order.order_final_price| currency }}&euro;</p>
												</span>
												<span v-else>
													<img class="img-responsive-disabled" src="/images/cake/cesta-vuota.png" title="Nessun prodotto ordinato" border="0">
												</span> 
											</span>
										</div>

									</div> <!-- row --> 
									<div class="alert alert-info text-right" v-if="results.user!=null && results.user.organization_id==organization.id"> 
										<span v-if="results.user.organization.template.payToDelivery==='ON' || user.template.organization.payToDelivery==='ON-POST'">
											Totale presunto della consegna: {{ results.delivery_final_price| currency  }}&euro;<br />
											(il totale effettivo per effettuare i pagamenti dev'essere confermato dal cassiere)
										</span>
										<span v-if="results.user.organization.template.payToDelivery==='POST'">
											Totale presunto della consegna: {{ results.delivery_final_price| currency  }}&euro;<br />
											(il totale effettivo per effettuare i pagamenti dev'essere confermato dal tesoriere)
										</span>
									</div>

								</div> <!-- all rows -->
							</div>
							</div>

						</div>
					</div> <!-- col-md-12 -->
				</div> <!-- row -->

			</section>
		</div>

	</main> 
</template>

<script>
import { mapActions } from "vuex";
import modalArticleOrdersCart from "../components/part/ModalArticleOrdersCart.vue";
import Menu from "../components/cms/Menu.vue";
import Organizations from "../components/common/Organizations.vue";
import Referents from "../components/part/Referents.vue";

export default {
  name: "deliveries",
  components: {
    modalArticleOrdersCart: modalArticleOrdersCart,
	Menu: Menu,
	Organizations: Organizations,
	Referents: Referents
  },
  data() {
    return {
	  slugGas: null,
	  organization: null,
      deliveries: null,
	  isLoading: false,
      isRunDeliveries: false,
      results: {
      	id: null,
      	datas: [],
		delivery_final_price: null,
		user: null

      },
      isRunOrders: false,
    };
  }, 
  mounted() {
    // console.log('mounted gas');
    // console.log('slugGas '+this.$route.params.slugGas);
    this.slugGas = this.$route.params.slugGas;
    if(this.slugGas=='')
        return;

    this.getOrganization();
  },
  methods: { 
	...mapActions(['showModalArticleOrdersCart', 'showOrHiddenModalArticleOrdersCart', 'addModalContent', 'clearModalContent']),
    clickShowOrHiddenModalArticleOrdersCart (order) {

		this.clearModalContent();

		var modalContent = {
			title: order.order.suppliers_organization.supplier.name,
			body: 'body '+order.id,
			order: order,
			footer: '',
			msg: ''
		}            


		this.addModalContent(modalContent);
		this.showOrHiddenModalArticleOrdersCart();              
	},
	getOrganization:function() {
		this.isLoading = true;

		let url = "/api/gas/organization/"+this.slugGas;
		// console.log(url, 'getOrganization url');
		axios
			.get(url)
			.then(response => {
				// console.log(response.data, 'getOrganization');
				if(typeof response.data !== "undefined") {
					this.organization = response.data.results;
					
					this.getDeliveries()
				}
				this.isLoading=false;
			})
			.catch(error => {
				this.isLoading=false;
				console.error("Error: " + error, 'getOrganization');
			});
        },	
	getDeliveries() {

		if(this.organization===null) 
			return;

		let params = {
			organization_id: this.organization.id
		}
		let url = '/api/deliveries/gets';	
		this.isRunDeliveries=true;
			axios
			.post(url, params)
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
			organization_id: this.organization.id,
			delivery_id: delivery_id
		};

		this.orders = [];

		let url = "/api/orders/gets";
		// console.log(url);
		axios
			.post(url, params)
			.then(response => {

				this.isRunOrders=false;
				if(typeof response.data !== "undefined") {
						var results = {
							delivery_id: delivery_id,
							datas: response.data.datas,
							delivery_final_price: response.data.delivery_final_price,
							user: response.data.user
						}
						this.results = results;
						// console.log(this.results);
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
h2 {
    height: 65px;
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