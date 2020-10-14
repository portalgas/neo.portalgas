<template>

	<div class="row">
	    <div class="col-sm-12 col-xs-12 col-md-12"> 
{{ order.suppliers_organization.supplier }}
        <div class="card mb-3">
          <div class="row no-gutters">
            <div class="col-md-2">
                <img v-if="order.suppliers_organization.supplier.img1 != ''"
                class="img-supplier" :src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
                :alt="order.suppliers_organization.supplier.name">
            </div>
            <div class="col-md-10">
               <div class="card-body">
                  <h5 class="card-title">
                      <a v-if="order.suppliers_organization.supplier.www!=''" target="_blank" v-bind:href="order.suppliers_organization.supplier.www" title="Vai al sito del produttore">
                        {{ order.suppliers_organization.name }} {{ order.order_type_id }}                      
                      </a>
                      <span v-if="order.suppliers_organization.supplier.www==''">
                        {{ order.suppliers_organization.name }} {{ order.order_type_id }}                      
                      </span>
                      <small class="card-text">
                        {{ order.suppliers_organization.supplier.descrizione }}
                      </small>                        
                  </h5>

                  <p class="card-text">
                      <span v-if="order.state_code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
                      <span v-if="order.state_code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.state_code=='OPEN-NEXT' && order.state_code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>

                      <button class="btn btn-primary float-right">{{ order.state_code }}</button>
                  <hr >
                    <div v-if="order.nota!=''"><strong>Nota:</strong> {{ order.nota }}</div>
                    <span v-if="order.hasTrasport=='N'" class="badge badge-secondary">Non ha spese di trasporto</span>
                    <span v-if="order.hasTrasport=='Y'" class="badge badge-warning">Ha spese di trasporto</span>

                    <span v-if="order.hasCostMore=='N'" class="badge badge-secondary">Non ha costi aggiuntivi</span>
                    <span v-if="order.hasCostMore=='Y'" class="badge badge-warning">Ha costi aggiuntivi</span>              
                  </p>
                  <p v-if="order.suppliers_organization.frequenza!=''" class="card-text">
                      <small class="text-muted"><strong>Frequenza</strong> {{ order.suppliers_organization.frequenza }}</small>
                  </p>
               </div> <!-- card-body -->
               <div class="card-footer text-muted bg-transparent-disabled">
                  <strong>Consegna</strong> {{ order.delivery.luogo }} il {{ order.delivery.data | formatDate }}
               </div> 
            </div> <!-- col-md-10 -->
          </div> <!-- row -->
        </div> <!-- card -->

	    </div> <!-- col... -->

	    <div class="col-sm-12 col-xs-2 col-md-2" 
		          v-for="article in articles"
		          :article="article"
		          :key="article.article_id"
		        >
		          <app-articles
		            v-bind:article="article">
                    <slot name="type">lllllllllllllllllllllll</slot>
                </app-articles>

	 	   </div> <!-- col... -->

	</div> <!-- row -->

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import articles from "../components/part/Articles.vue";

export default {
  name: "app-order",
  data() {
    return {
      order_id: 0,
      order: {},
      articles: Object,
      displayList: false
    };
  },
  components: {
    appArticles: articles,
  },  
  computed: {
    ...mapGetters(["getOrder"]),
    getStoreOrder() {
      return this.getOrder;
    }
  },  
  mounted() {
  	this.order_id = this.$route.params.order_id;
    console.log(this.getStoreOrder);
    console.log(this.getStoreOrder.id);
    if(this.order_id!=this.getStoreOrder.id) {
      this.getAjaxOrder();
      this.getsAjaxArticles();    
    }

    // const cartArticles = JSON.parse(localStorage.getItem('cartArticles'));
  },
  methods: {
    ...mapActions(["addArticle"]),
    getAjaxOrder() {
      let url = "/admin/api/orders/get";
      let params = {
        order_id: this.order_id
      };
      axios
        .post(url, params)
        .then(response => {
          console.log(response.data);
          if(typeof response.data !== "undefined") {
            this.order = response.data;
            console.log(this.order);
          }
        })
        .catch(error => {
          console.error("Error: " + error);
        });    
    },
    getsAjaxArticles() {
      let url = "/admin/api/orders/getArticlesOrdersByOrderId";
      let params = {
        order_id: this.order_id
      };
      axios
        .post(url, params)
        .then(response => {
          console.log(response.data);
          if(typeof response.data[0] !== "undefined" && typeof response.data[0].ids !== "undefined") {
            this.articles = response.data;
            console.log(this.articles);
          }
        })
        .catch(error => {
          console.error("Error: " + error);
        });
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
</style>