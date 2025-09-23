<template>

<main>

  <ul class="link-top">
    <li v-if="order_type_id!=9">
      <router-link to="/fai-la-spesa" class="btn btn-primary">Torna alla consegne</router-link>
    </li>
    <li v-if="order_type_id==9">
      <router-link to="/social-market" class="btn btn-primary">Torna all'elenco dei produttori</router-link>
    </li>
    <li v-if="order_type_id!=9">
      <a class="btn btn-primary" id="btn-cart-previous" :href="'/admin/joomla25Salts?scope=FE&c_to=/home-'+j_seo+'/fai-la-spesa-'+j_seo">Passa alla precedente versione per gli acquisti</a>
    </li>
  </ul>

  <div class="clearfix"></div>

  <!--               -->
  <!-- elenco ordini -->
  <!--               -->
  <div class="col-sm-12 col-xs-12 col-md-12">
    <div class="form-group">

        <b-dropdown text="Vai all'ordine" block class="m-md-2" menu-class="w-100"
        v-if="!isRunOrders && orders.length>0">

          <template v-for="(order, index) in orders"
              :value="order.id">
            <a :href="'/order/'+order.order_type_id + '/' + order.id" class="order-change">
              <b-dropdown-item-button block >

                <img style="max-width:75px" v-if="order.suppliers_organization.supplier.img1 != ''"
                  :src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
                  :alt="order.suppliers_organization.name">

                  {{ order.suppliers_organization.name }}&nbsp;

                    <b-badge variant="secondary" v-if="order.order_type_id!=9 && order.delivery.sys=='Y'" style="float:right">
                        {{ order.delivery.luogo }}
                    </b-badge>
                    <b-badge variant="secondary" v-if="order.order_type_id!=9 && order.delivery.sys!='Y'" style="float:right">
                        {{ order.delivery.luogo }} il {{ order.delivery.data | formatDate }}
                    </b-badge>
              </b-dropdown-item-button>
            </a>
          </template>

        </b-dropdown>
        <div v-if="isRunOrders" class="box-spinner">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
  </div> <!-- col-sm-12 col-xs-12 col-md-12 -->
  <!--               -->
  <!-- elenco ordini -->
  <!--               -->

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
                <div class="content-img-supplier">
                  <img v-if="order.suppliers_organization.supplier.img1 != ''"
                    class="img-supplier" :src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
                    :alt="order.suppliers_organization.supplier.name">
                </div>

            </div>
            <div class="col-md-10">
               <div class="card-body" :class="'type-'+order.order_type.name">
                  <h5 class="card-title">
                      <a target="_blank" v-bind:href="'https://neo.portalgas.it/site/produttore/'+order.suppliers_organization.supplier.slug" title="vai alla pagina del produttore">
                        {{ order.suppliers_organization.name }}
                      </a>
                      <small class="card-text">
                        {{ order.suppliers_organization.supplier.descrizione }}
                      </small>
                  </h5>

                  <!--            -->
                  <!--    D E S   -->
                  <!--            -->

                  <p v-if="order.des_orders_organization!=null" class="card-text">
                      <div v-if="order.des_orders_organization!=null">
                        DES {{ order.des_orders_organization.de.name }} terminerà {{ order.des_orders_organization.des_order.data_fine_max | formatDate }}
                      </div>

                      <div v-if="order.all_des_orders_organizations!=null">
                        <ul class="list-unstyled">
                          <li v-for="(all_des_orders_organization, index) in order.all_des_orders_organizations">
                            <a target="_blank" v-bind:href="all_des_orders_organization.organization.www" title="vai al sito del G.A.S.">

                              <div class="content-img-organization">
                                  <img v-if="all_des_orders_organization.organization.img1 != ''"
                                  class="img-organization" :src="appConfig.$siteUrl+'/images/organizations/contents/'+all_des_orders_organization.organization.img1"
                                  :alt="all_des_orders_organization.organization.name" />
                              </div>

                              {{ all_des_orders_organization.organization.name }}
                            </a>
                          </li>
                        </ul>
                      </div>
                  </p>
                  <!--            -->
                  <!--    D E S   -->
                  <!--            -->

                  <!--                        -->
                  <!--    P R O M O T I O N   -->
                  <!--                        -->
                  <p v-if="order.prod_gas_promotion!=null" class="card-text">
                    {{ order.prod_gas_promotion.name }}
                    terminerà {{ order.prod_gas_promotion.data_fine | formatDate }}
                  </p>
                  <!--                        -->
                  <!--    P R O M O T I O N   -->
                  <!--                        -->


                  <p class="card-text">

                      <div class="float-right mb-2">

                       <span v-if="isRunTotCart" class="box-spinner">
                         <div class="spinner-border text-info" role="status">
                           <span class="sr-only">Loading...</span>
                         </div>
                       </span>

                       <span v-if="!isRunTotCart" class="badge badge-pill badge-info">Totale: {{ tot_cart | currency }} &euro;</span>

                        <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
                        <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>
                      </div>

                      <span v-if="order.order_type_id!=9 && order.order_state_code.code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
                      <span v-if="order.order_type_id!=9 && order.order_state_code.code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_type_id!=9 && order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_type_id!=9 && order.order_state_code.code=='RI-OPEN-VALIDATE'">Riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>

                  <hr style="clear: both;">

                    <div v-if="order.mail_open_testo!=''" class="alert alert-info">
                      <p v-html="$options.filters.html(order.mail_open_testo)"></p>

                      <!--                         -->
                      <!-- S O C I A L M A R K E T -->
                      <!--                         -->
                      <div class="quote-wrapper" v-if="order.order_type_id==9">
                        <blockquote class="text">
                          <p>Ti piace questo produttore? consigliato al tuo GAS!</p>
                        </blockquote>
                      </div>
                    </div>

                    <span v-if="order.suppliers_organization.isSupplierOrganizationCashExcluded!=null && order.suppliers_organization.isSupplierOrganizationCashExcluded" class="badge badge-secondary">Escluso dal prepagato</span>
                    <span v-if="order.suppliers_organization.isSupplierOrganizationCashExcluded!=null && !order.suppliers_organization.isSupplierOrganizationCashExcluded" class="badge badge-secondary">Gestito con il prepagato</span>

                    <span v-if="order.order_type_id!=9 && order.hasTrasport=='N'" class="badge badge-secondary">Non ha spese di trasporto</span>
                    <span v-if="order.order_type_id!=9 && order.hasTrasport=='Y'" class="badge badge-warning">Ha spese di trasporto</span>

                    <span v-if="order.order_type_id!=9 && order.hasCostMore=='N'" class="badge badge-secondary">Non ha costi aggiuntivi</span>
                    <span v-if="order.order_type_id!=9 && order.hasCostMore=='Y'" class="badge badge-warning">Ha costi aggiuntivi</span>
                  </p>

                  <p v-if="order.order_type_id!=9 && order.suppliers_organization.frequenza!=''" class="card-text">
                      <small class="text-muted"><strong>Frequenza</strong> {{ order.suppliers_organization.frequenza }}</small>
                  </p>
               </div> <!-- card-body -->
               <div class="card-footer text-muted bg-transparent-disabled">
                  <strong>Consegna</strong>
                  <span v-if="order.delivery.sys=='Y'">
                      {{ order.delivery.luogo }}
                  </span>
                  <span v-if="order.delivery.sys!='Y'">
                      {{ order.delivery.luogo }} il {{ order.delivery.data | formatDate }}
                  </span>
               </div>

               <referents v-if="order.referents!=null"
							          :referents="order.referents" />

                <!--                        -->
                <!--        DISTANCE        -->
                <!--                        -->
                <div v-if="order.distance!=null">
                    <i class="fas fa-truck"></i> I tuoi acquisti hanno percorso {{ order.distance.distance }} Km
                </div>

            </div> <!-- col-md-10 -->
          </div> <!-- row -->
        </div> <!-- card -->

      </div> <!-- col... -->
    </div> <!-- row -->

    <div class="row">
        <div class="col-12 col-md-12 col-lg-8">
            <app-search-article-orders @search="onSearch" />
        </div>
        <div class="col-8 col-md-8 col-lg-2">
            <app-sort-article-orders @changeSort="onChangeSort" />
        </div>
        <div class="col-4 col-md-4 col-lg-2">
            <app-view-article-orders :viewList="viewList" @changeView="onChangeView" />
        </div>
    </div>
    <div class="row" v-if="order_type_id!=9"> <!-- SOCIALMARKET -->
        <div class="col-12 col-md-12 col-lg-12">
            <app-search-category-articles @searchCategoryArticles="onSearchCategoryArticles" :order="order" />
        </div>
    </div>
    <div class="row" v-if="order_type_id!=9"> <!-- SOCIALMARKET -->
        <div class="col-12 col-md-12 col-lg-10">
            <app-search-article-types @searchArticleTypes="onSearchArticleTypes" :order="order" />
        </div>
        <div class="col-12 col-md-12 col-lg-2 text-right">
            <span v-if="articles.length==1" class="badge badge-pill badge-primary">Visualizzato {{ articles.length }} articolo</span>
            <span v-else="articles.length>0" class="badge badge-pill badge-primary">Visualizzati {{ articles.length }} articoli</span>
        </div>
    </div>

    <div class="row">

          <!-- modalita list -->
          <div class="col-sm-12 col-xs-12 col-md-12"
                  v-if="viewList"
                  v-for="(article, index) in articles"
                  :article="article"
                  :key="article.article_id"
                >
                <div class="box-article-order">

                  <app-article-order-list
                    v-bind:article="article"
                    v-bind:order="order"
                    v-on:emitCartSave="emitCartSave()"
                    >
                    </app-article-order-list>

                </div>
          </div>


          <!-- modalita grid -->
          <div class="col-sm-12 col-xs-2 col-md-3"
                  v-if="!viewList"
                  v-for="(article, index) in articles"
                  :article="article"
                  :key="article.article_id"
                >
                <div class="box-article-order" :class="{even: index % 2, odd: !(index % 2)}">

                  <app-article-order
                    v-bind:article="article"
                    v-bind:order="order"
                    v-on:emitCartSave="emitCartSave()"
                    >
                    </app-article-order>

                </div>
          </div>


          <div class="col-sm-12 col-xs-12 col-md-12" v-if="!isRunArticles && articles.length==0">
            <div class="alert alert-warning" role="alert">
                <span v-if="order!=null && order.order_state_code.code=='RI-OPEN-VALIDATE'">Tutti i colli dell'ordine sono completati</span>
                <span v-if="order!=null && order.order_state_code.code!='RI-OPEN-VALIDATE'">
                    <span v-if="q!='' || search_categories_article_id>0 || search_article_types_ids.length>0">Nessun articolo trovato con i filtri impostati</span>
                    <span v-else>L'ordine non ha articoli ordinabili</span>
                </span>
            </div>
          </div>

          <div v-if="isRunArticles" class="box-spinner">
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>

    </div> <!-- row -->

    <!--
      TOUR
    <v-tour name="myTour" :steps="steps" :options="tourOptions" :callbacks="tourCallbacks"></v-tour>
    -->

  </main>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import articleOrder from "../components/part/ArticleOrder.vue";
import articleOrderList from "../components/part/ArticleOrderList.vue";
import searchArticleOrders from "../components/part/SearchArticleOrders.vue";
import searchCategoryArticles from "../components/part/SearchCategoryArticles.vue";
import searchArticleTypes from "../components/part/SearchArticleTypes.vue";
import sortArticleOrders from "../components/part/SortArticleOrders.vue";
import viewArticleOrders from "../components/part/ViewArticleOrders.vue";
import Referents from "../components/part/Referents.vue";

export default {
  name: "app-order",
  data() {
    return {
      j_seo: '',
      order_type_id: 0,
      order_id: 0,
      order: null,
      orders: [],
      articles: [],
      page: 1,
      sort: null,
      isScrollFinish: false,
      tot_cart: 0,
      isRunTotCart: false,
      isRunOrders: false,
      isRunOrder: false,
      isRunArticles: false,
      displayList: false,
      q: null, // parola ricerca
      search_categories_article_id: 0, // filtro categoria
      search_article_types_ids: [], // filtro tipologia
      viewList: false, // di default e' vista griglia

      cookie_name: 'tour',
      scope: 'order',
      tourOptions: {
        startTimeout: 3,
        highlight: true,
        useKeyboardNavigation: false,
        labels: {
          buttonSkip: 'Salta tour',
          buttonPrevious: 'Precedente',
          buttonNext: 'Prossimo',
          buttonStop: 'Finito'
        }
      },
      tourCallbacks: {
        onSkip: this.onSkip,
        onFinish: this.onFinish,
      },
      steps:[
         {
            "target":"#btn-cart-previous",
            "content": "Sei un nostalgico? Torna alla precedente versione per gli acquisti"
         },
         {
            "target":"#btn-view-grid",
            "content": "Se usi lo smartphone, visualizza gli articoli in formato griglia"
         },
         {
            "target":"#btn-view-list",
            "content": "Se usi il computer, visualizza gli articoli in formato lista"
         }
      ],

    };
  },
  components: {
    appArticleOrder: articleOrder,
    appArticleOrderList: articleOrderList,
    appSearchArticleOrders: searchArticleOrders,
    appSearchCategoryArticles: searchCategoryArticles,
    appSearchArticleTypes: searchArticleTypes,
    appViewArticleOrders: viewArticleOrders,
    appSortArticleOrders: sortArticleOrders,
    referents: Referents
  },/*
  computed: {
    viewListCookie: function() {
         * recupero modalita' visualizzazione griglia / lista da cookie
        return this.getCookie('viewList');
    }
  }, */
  mounted() {
    this.order_type_id = this.$route.params.order_type_id;
    this.order_id = this.$route.params.order_id;
    // console.log('mounted route.params.order_type_id '+this.order_type_id+' route.params.order_id '+this.order_id);

    this.viewList = this.getCookie('viewList');

    this.getGlobals();
    this.getAjaxOrder();
    this.totalPrice();
    this.scroll();

    var found = this.checkCookieTour(this.scope);
    console.log('checkCookieTour found '+found+' per lo scope '+this.scope);
    /*
    if(!found)
      this.$tours['myTour'].start()
    */
  },
  methods: {
    getGlobals() {
      /*
       * variabile che arriva da cake, dichiarata come variabile in Layout/vue.ctp, in app.js settata a window.
       * recuperata nei components con getGlobals()
       */
      this.j_seo = window.j_seo;
    },
    emitCartSave() {
      // console.log('emitCartSave', 'Order');

      this.totalPrice();
    },
    totalPrice() {

      /*
       * visualizzando un tot di articoli alla volta il calcolo del totale e' solo parziale
      return this.$options.filters.currency(this.articles.reduce(
        (current, next) => current + (next.cart.qta_new * next.price),
        0
      ));
      */
      this.isRunTotCart = true;

      let url = "/admin/api/carts/getTotImportByOrderId";

      let params = {
        order_type_id: this.order_type_id,
        order_id: this.order_id
      };

      axios
          .post(url, params)
          .then(response => {

            this.isRunTotCart = false;

            // console.log(response.data);
            if(typeof response.data !== "undefined") {
               this.tot_cart = response.data.results;
            }
            else {
              console.error("Error: " + response.message);
            }
          })
          .catch(error => {
            this.isRunOrder = false;
            console.error("Error: " + error);
          });
    },
    onSearch: function(q) {
      this.articles = [];
      this.page = 1;
      this.q = q;
      this.scroll();
      this.isScrollFinish = false;
      /* console.log('onSearch '+q); */
    },
    onSearchCategoryArticles: function(search_categories_article_id) {
      this.articles = [];
      this.page = 1;
      this.search_categories_article_id = search_categories_article_id;
      this.scroll();
      this.isScrollFinish = false;
      /* console.log('onSearchCategoryArticles '+search_categories_article_id); */
    },
    onSearchArticleTypes: function(search_article_types_ids) {
        this.articles = [];
        this.page = 1;
        this.search_article_types_ids = search_article_types_ids;
        this.scroll();
        this.isScrollFinish = false;
        /* console.log('onSearchArticleTypes '+search_article_types_ids); */
    },
    onChangeView: function(viewList) {
      this.viewList = viewList;
      /* console.log('onChangeView '+this.viewList); */
    },
    onChangeSort: function(sort) {
      this.sort = sort;
      console.log('sort '+this.sort);
      this.onSearch();
    },
    scroll() {
      /* console.log('scroll page '+this.page+' isRunArticles '+this.isRunArticles+' isScrollFinish '+this.isScrollFinish); */
      if(this.isScrollFinish || this.isRunArticles)
        return;

      if (this.page==1) {
         this.getsAjaxArticles();
      }

        window.onscroll = () => {
          let scrollTop = Math.floor(document.documentElement.scrollTop);
          let bottomOfWindow = scrollTop + window.innerHeight > (document.documentElement.offsetHeight - 10);
          // console.log((scrollTop + window.innerHeight)+' '+(document.documentElement.offsetHeight - 10));

          /*
          scrollTop    height to top
          innerHeight  height windows
          offsetHeight height page
          console.log('document.documentElement.scrollTop '+scrollTop);
          console.log('window.innerHeight '+window.innerHeight);
          console.log('document.documentElement.offsetHeight '+document.documentElement.offsetHeight);
          console.log('bottomOfWindow '+bottomOfWindow);
          */

          if (bottomOfWindow && !this.isRunArticles && !this.isScrollFinish) {
              this.getsAjaxArticles();
          }
        };
    },
    getAjaxOrder() {

      this.isRunOrder = true;

      let url = "/admin/api/orders/get";

      let params = {
        order_type_id: this.order_type_id,
        order_id: this.order_id
      };

      axios
        .post(url, params)
        .then(response => {

          this.isRunOrder = false;

          // console.log(response.data);
          if(typeof response.data !== "undefined") {
            this.order = response.data;

            this.getListOrders();
          }
          else {
            console.error("Error: " + response.message);
            this.isRunOrder = false;
          }
        })
        .catch(error => {
          this.isRunOrder = false;
          console.error("Error: " + error);
        });
    },
    getsAjaxArticles() {

      this.isRunArticles = true;

      let url = "/admin/api/orders/getArticlesOrdersByOrderId";

      let params = {
        order_type_id: this.order_type_id,
        order_id: this.order_id,
        page: this.page,
        q: this.q,
        search_categories_article_id: this.search_categories_article_id,
        search_article_types_ids: this.search_article_types_ids,
        sort: this.sort
      };
      // console.log('getsAjaxArticles url '+url)

      axios
        .post(url, params)
        .then(response => {

          this.isRunArticles = false;

          // console.log('getsAjaxArticles');
          // console.log(response.data.length);
          // console.log(response.data[0]);
          // console.log(response.data[0].ids);
          if(response.data.length>0) {
              if(typeof response.data[0] !== "undefined" && typeof response.data[0].ids !== "undefined") {
                var data = response.data;
                for (var i = 0; i < data.length; i++) {
                    this.articles.push(data[i]);
                }
                this.page++;
                this.isScrollFinish = false;
              }
          }
          else {
            /*
             * se ho effettuato una ricerca e non ho trovato nulla
             */
             if(this.q!='' || this.search_categories_article_id>0 || this.search_article_types_ids.length>0)
                 this.isScrollFinish = false;
             else
                this.isScrollFinish = true;
          }

        })
        .catch(error => {
          this.isRunArticles = false;
          console.error("Error: " + error);
          this.isScrollFinish = true;
        });
    },
    getListOrders() {

        if(this.order==null)
          return;

        this.isRunOrders=true;

        let params = {
          /*
           * per gli ordini per produttore non ho la consegna
           delivery_id: this.order.delivery_id
          */
        };

        this.orders = [];

        let url = '/admin/api/orders/gets/' + this.order_type_id;

      // console.log(url);
        axios
          .post(url, params)
          .then(response => {

            this.isRunOrders=false;

            // console.log(response.data);
            if(typeof response.data !== "undefined") {
              this.orders = response.data;
              // console.log(this.orders);
          }
        })
        .catch(error => {

          this.isRunOrders=false;

          console.error("Error: " + error);
        });

    },
    /*
     * ctrl cooies per viewList / tour
     */
    getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            var viewList = c.substring(name.length, c.length);

            if(viewList=='false')
                return false;
            else
            if(viewList=='true')
              return true;
            else
              return viewList;
          }
        }
        return "";
    },

    /*
     * event: btn CLOSE
     * acconda i valori del cookies key=value'+this.delimiter+'value...
     */
    addCookie: function(name, value) {

      var found = false;
      var value_old = this.getCookie(name);
      var value_new = '';
      /* console.log("addCookie scope corrente " + value); */
      /* console.log("addCookie oldCookies value " + value_old); */

      if(value_old=='') {
        value_new = value;
      }
      else
      if(value_old!='') {
        if(value_old.indexOf(this.delimiter)>0) {
          var value_olds = value_old.split(this.delimiter);
          for(var i = 0; i < value_olds.length; i++)
          {
            if(value_olds[i].toLowerCase() == value.toLowerCase())
              found = true;
              /* console.log(value_olds[i]); */
          }
        }
        else {
          if(value_old.toLowerCase() == value.toLowerCase())
            found = true;
        }

        if(!found) {
          value_new = value_old + this.delimiter + value;
          value_new = value_new.toLowerCase();
        }
      }

      if(!found) {
        /* console.log("addCookie newCookies value " + value_new); */
        this.setCookie(name, value_new, 365);
      }
      else {
        /* console.log("addCookie value gia' presente "); */
      }
    },
    setCookie: function (name, value, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays*24*60*60*1000));
      var expires = "expires="+ d.toUTCString();
      document.cookie = name + "=" + value + ";" + expires + ";path=/";
    },
    checkCookieTour: function(value) {
      var found = false;
      var values = this.getCookie(this.cookie_name);
      /* console.log('checkCookieTour get cookie_name '+this.cookie_name); */
      if(values=='') {
        console.log('not found cookie_name '+this.cookie_name+' => eseguo tour');
      }
      else {
        if(values.indexOf(this.delimiter)>0) {
          var value_splits = values.split(this.delimiter);
          for(var i = 0; i < value_splits.length; i++)
          {
            if(value_splits[i].toLowerCase() == value.toLowerCase())
              found = true;
          }
        }
        else {
          if(values.toLowerCase() == value.toLowerCase())
            found = true;
        }
      }

      return found;
    },
    onSkip () {
      this.addCookie(this.cookie_name, this.scope);
    },
    onFinish () {
      this.addCookie(this.cookie_name, this.scope);
    },
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
      lowerCase : function(value) {
        return value.toLowerCase().trim();
      },
      html(text) {
        return text;
      },
  }
};
</script>

<style scoped>
ul.link-top {
  margin: 0 25px;
  padding: 0;
  padding-bottom: 35px;
}
ul.link-top li {
  list-style: none;
  padding: 5px;
  border-radius: 5px;
}
ul.link-top li:first-child {
  float: left;
}
ul.link-top li:last-child {
  float: right;
}
ul.link-top li:hover {
  background-color: #0a659e;
  color: #fff !important;
}
ul.link-top li a:hover {
  color: #fff !important;
  text-decoration: none;
}
.card {
  border: none;
}
.card-body.type-PROMOTION {
  background-image: url("/img/promotion-100w-110h.png");
  background-repeat: no-repeat, no-repeat;
  background-position: right top;
}
@media screen and (max-width: 600px) {
  .card-body.type-PROMOTION {
     background-position: right bottom;
  }
}
.progressBar {
  background-color: #0a659e;
}
@media screen and (max-width: 600px) {
  .box-article-order {
  }
  .even {
    background: #eee;
  }
  .odd {
    background: #ffffff;
  }
}

.v-tour__target--highlighted {
  box-shadow: 0 0 0 99999px rgba(0,0,0,.4);
}



.quote-wrapper {
  position: absolute;
  top: -50px;
  right: 1px;
  width: 170px;
  height: 170px;
  margin: 10vh auto 0; /*OPTIONAL MARGIN*/
}

.text {
  width: 100%;
  height: 100%;
  /*BLUE BG*/
  background: radial-gradient(
      ellipse at center,
      rgba(0, 128, 172, 1) 0%,
      rgba(0, 128, 172, 1) 70%,
      rgba(0, 128, 172, 0) 70.3%
  );
  /*RED BG
  background: radial-gradient(
    ellipse at center,
    rgba(210, 20, 20, 1) 0%,
    rgba(210, 20, 20, 1) 70%,
    rgba(210, 20, 20, 0) 70.3%
  );*/
  position: relative;
  margin: 0;
  color: white;
}

.text p {
  height: 100%;
  font-size: 18px;
  line-height: 1.25;
  padding: 0;
  text-align: center;
  font-style: italic;
  text-shadow: 0.5px 0.5px 1px rgba(0, 0, 0, 0.3);
}

.text::before {
  content: "";
  width: 50%;
  height: 100%;
  float: left;
  shape-outside: polygon(
      0 0,
      98% 0,
      50% 6%,
      23.4% 17.3%,
      6% 32.6%,
      0 50%,
      6% 65.6%,
      23.4% 82.7%,
      50% 94%,
      98% 100%,
      0 100%
  );
  shape-margin: 7%;
}

.text p::before {
  content: "";
  width: 50%;
  height: 100%;
  float: right;
  shape-outside: polygon(
      2% 0%,
      100% 0%,
      100% 100%,
      2% 100%,
      50% 94%,
      76.6% 82.7%,
      94% 65.6%,
      100% 50%,
      94% 32.6%,
      76.6% 17.3%,
      50% 6%
  );
  shape-margin: 7%;
}

.quote-wrapper::before {
  content: "\201C";
  font-size: 270px;
  height: 82px;
  line-height: 0.78;
  line-height: 1;
  position: absolute;
  top: -48px;
  left: 0;
  z-index: 1;
  font-family: sans-serif, serif;
  color: #ccc;
  opacity: 0.9;
}

@media (min-width: 850px) {
  .quote-wrapper {
    width: 170px;
    height: 170px;
  }

  .quote-wrapper::before {
    font-size: 250px;
  }

  .text p {
    font-size: 20px;
  }
}

@media (max-width: 500px) {
  .quote-wrapper {
    position: relative;
  }
}
</style>
