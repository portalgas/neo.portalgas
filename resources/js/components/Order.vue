<template>

<div>

  page: {{ page }}

	<div class="row">
	    <div class="col-sm-12 col-xs-12 col-md-12"> 

        <div v-if="isRunOrder" class="box-spinner"> 
          <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
          </div>  
        </div>

        <div v-if="!isRunOrder && order!=null" class="card mb-3">
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
                      <span v-if="order.order_state_code.code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
                      <span v-if="order.order_state_code.code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">Riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>

                      <button class="badge badge-primary float-right">{{ order.order_state_code.code }} {{ order.order_state_code.name }}</button>
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
    </div> <!-- row -->

    <div class="row">
        <div  class="col-12">
            <app-search-articles @search="onSearch" />
        </div>
    </div>

    <div class="row">

          <div v-if="isRunArticles" class="box-spinner"> 
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>  
          </div>

    	    <div v-if="!isRunArticles" class="col-sm-12 col-xs-2 col-md-3" 
    		          v-for="article in articles"
    		          :article="article"
    		          :key="article.article_id"
    		        >
    		          <app-articles
    		            v-bind:article="article">
                    </app-articles>
          </div> 

    </div> <!-- row -->

	</div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import articles from "../components/part/Articles.vue";
import searchArticles from "../components/part/SearchArticles.vue";

export default {
  name: "app-order",
  data() {
    return {
      order_id: 0,
      order: null,
      articles: [],
      page: 1,
      isRunOrder: false,   
      isRunArticles: false,   
      displayList: false,
      q: null // parola ricerca
    };
  },
  // props: ['q'],
  components: {
    appArticles: articles,
    appSearchArticles: searchArticles
  },  
  computed: {
    ...mapGetters(["getOrder"]),
    getStoreOrder() {
      return this.getOrder;
    }
  },  
  mounted() {
  	this.order_id = this.$route.params.order_id;
    console.log('route.params.order_id  '+this.order_id);
    console.log('getStoreOrder');
    console.log(this.getStoreOrder);
    
    if(typeof this.getStoreOrder !=="undefined" && this.order_id!=this.getStoreOrder.id) {
      console.log('RICARICO per order store != order route.params - getStoreOrder.id '+this.getStoreOrder.id);
      this.getAjaxOrder();
      // this.getsAjaxArticles();    
      this.scroll();
    }
    else {
      console.log('CARICO perche order in store = undefined ');
      this.scroll();    
    }
    // const cartArticles = JSON.parse(localStorage.getItem('cartArticles'));
  },
  methods: {
    onSearch: function(q) {
      this.articles = [];
      this.page=1;
      this.q = q;
      this.scroll();
    },      
    scroll () {

      if (this.page==1) {
         this.getsAjaxArticles();
      }

      window.onscroll = () => {
        let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

        console.log('scroll bottomOfWindow '+bottomOfWindow);
        /*
        scrollTop    get the number of pixels the content of a <div> element is scrolled horizontally and vertically
        innerHeight  get the current frame's height and width
        offsetHeight get the height of document, including padding and border
        console.log('document.documentElement.scrollTop '+document.documentElement.scrollTop);
        console.log('window.innerHeight '+window.innerHeight);
        console.log('document.documentElement.offsetHeight '+document.documentElement.offsetHeight);
        console.log('bottomOfWindow '+bottomOfWindow);
        */

        if (bottomOfWindow) {
              this.getsAjaxArticles();
        }
      };  
    },
    getAjaxOrder() {

      this.isRunOrder = true;

      let url = "/admin/api/orders/get";
      let params = {
        order_id: this.order_id
      };
      axios
        .post(url, params)
        .then(response => {

          this.isRunOrder = false;

          console.log(response.data);
          if(typeof response.data !== "undefined") {
            this.order = response.data;
          }
        })
        .catch(error => {
          this.isRunOrder = false;
          console.error("Error: " + error);
        });    
    },
    getsAjaxArticles() {

      // this.isRunArticles = true;

      let url = "/admin/api/orders/getArticlesOrdersByOrderId";
      let params = {
        order_id: this.order_id,
        page: this.page,
        q: this.q
      };

      axios
        .post(url, params)
        .then(response => {

          this.isRunArticles = false;

          console.log('getsAjaxArticles');
          console.log(response.data);
          // console.log(response.data[0]);
          console.log(response.data[0].ids);
          if(typeof response.data[0] !== "undefined" && typeof response.data[0].ids !== "undefined") {
            var data = response.data;
            for (var i = 0; i < data.length; i++) {
                this.articles.push(data[i]);
            }          
            this.page++;
          }
        })
        .catch(error => {
          this.isRunArticles = false;
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
      },       
  }
};
</script>

<style scoped>
.card { 
  border: none;
}
</style>