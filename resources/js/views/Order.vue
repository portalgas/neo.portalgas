<template>

<div>

  <ul class="link-top">
    <li>
      <router-link to="/fai-la-spesa">Torna alla consegne</router-link>
    </li>
    <li>
      <a id="btn-cart-previous" :href="'/admin/joomla25Salts?scope=FE&c_to=/home-'+j_seo+'/fai-la-spesa-'+j_seo">Passa alla precedente versione per gli acquisti</a>
    </li>
  </ul>

  <div class="clearfix"></div>

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
                    class="img-supplier" :src="'https://www.portalgas.it/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
                    :alt="order.suppliers_organization.supplier.name">
                </div>

            </div>
            <div class="col-md-10">
               <div class="card-body" :class="'type-'+order.order_type.name">
                  <h5 class="card-title">
                      <a v-if="order.suppliers_organization.supplier.www!=''" target="_blank" v-bind:href="order.suppliers_organization.supplier.www" title="vai al sito del produttore">
                        {{ order.suppliers_organization.name }}
                      </a>
                      <span v-if="order.suppliers_organization.supplier.www==''">
                        {{ order.suppliers_organization.name }}
                      </span>
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
                                  class="img-organization" :src="'https://www.portalgas.it/images/organizations/contents/'+all_des_orders_organization.organization.img1"
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

                      <div class="float-right">
                        <span class="badge badge-pill badge-info">Totale: {{ totalPrice() }} &euro;</span>

                        <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
                        <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>  
                      </div>

                      <span v-if="order.order_state_code.code=='OPEN-NEXT'">Aprirà {{ order.data_inizio | formatDate }} </span>
                      <span v-if="order.order_state_code.code=='OPEN'">chiuderà {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">Data chiusura {{ order.data_fine | formatDate }}</span>
                      <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">Riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>
                  <hr >
                    <div v-if="order.mail_open_testo!=''" class="alert alert-info" v-html="$options.filters.html(order.mail_open_testo)"></div>

                    <span v-if="order.suppliers_organization.isSupplierOrganizationCashExcluded!=null && order.suppliers_organization.isSupplierOrganizationCashExcluded" class="badge badge-secondary">Escluso dal prepagato</span>
                    <span v-if="order.suppliers_organization.isSupplierOrganizationCashExcluded!=null && !order.suppliers_organization.isSupplierOrganizationCashExcluded" class="badge badge-secondary">Gestito con il prepagato</span>

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
                  <strong>Consegna</strong> 
                  <span v-if="order.delivery.sys=='Y'">
                      {{ order.delivery.luogo }}
                  </span>
                  <span v-if="order.delivery.sys!='Y'">
                      {{ order.delivery.luogo }} il {{ order.delivery.data | formatDate }}
                  </span>
               </div> 

                <!--                        -->
                <!--    R E F E R E N T I   -->
                <!--                        -->
                <p v-if="order.referents!=null" class="card-text">
                  <!-- i class="fas fa-user-friends"></i -->
                  <ul class="list-inline">
                      <li class="list-inline-item" v-for="referent in order.referents">
                        
                          <span v-if="referent.type!='referente'">({{ referent.type }})</span>
                            {{ referent.name }} 
                            <a v-if="referent.email!=''" class="a-mailto" target="_blank" :href="'mailto:'+referent.email">{{ referent.email }}</a>
                            <span v-if="referent.phone_satispay">
                               &nbsp;{{ referent.phone_satispay }}  
                              <img src="/img/satispay-ico.png" title="il referente ha Satispy" />
                            </span>
                            <span v-if="referent.phone_satispay==null">
                               &nbsp;{{ referent.phone }}  
                            </span>
                        
                      </li>
                  </ul>
                </p>
                <!--                        -->
                <!--    R E F E R E N T I   -->
                <!--                        -->


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
        <div class="col-10">
            <app-search-article-orders @search="onSearch" />
        </div>
        <div class="col-2">
            <app-view-article-orders :viewList="viewList" @changeView="onChangeView" />
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
                    v-bind:order="order">
                    </app-article-order>
                    
                </div> 
          </div>


          <div class="col-sm-12 col-xs-12 col-md-12" v-if="!isRunArticles && articles.length==0">
            <div class="alert alert-warning" role="alert">
                <span v-if="order!=null && order.order_state_code.code=='RI-OPEN-VALIDATE'">Tutti i colli dell'ordine sono completati</span>
                <span v-if="order!=null && order.order_state_code.code!='RI-OPEN-VALIDATE'">L'ordine non ha articoli ordinabili</span>
            </div>
          </div>

          <div v-if="isRunArticles" class="box-spinner"> 
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>  
          </div>

    </div> <!-- row -->

    <v-tour name="myTour" :steps="steps" :options="tourOptions" :callbacks="tourCallbacks"></v-tour>

  </div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import articleOrder from "../components/part/ArticleOrder.vue";
import articleOrderList from "../components/part/ArticleOrderList.vue";
import searchArticleOrders from "../components/part/SearchArticleOrders.vue";
import viewArticleOrders from "../components/part/ViewArticleOrders.vue";

export default {
  name: "app-order",
  data() {
    return {
      j_seo: '',
      order_type_id: 0,
      order_id: 0,
      order: null,
      articles: [],
      page: 1,
      isScrollFinish: false,   
      isRunOrder: false,   
      isRunArticles: false,   
      displayList: false,
      q: null, // parola ricerca
      viewList: false, // di default e' vista griglia 

      cookie_name: 'tour',
      scope: 'order',
      tourOptions: {
        startTimeout: 3,
        useKeyboardNavigation: false,
        labels: {
          buttonSkip: 'Salta tour',
          buttonPrevious: 'Precedente',
          buttonNext: 'Prossimo',
          buttonStop: 'Finito'
        }
      },
      tourCallbacks: {
        onStart: this.onStart,
        onSkip: this.onSkip,
        onFinish: this.onFinish,
      },     
      steps:[
         {
            "target":"#btn-view-grid",
            "content": "Se usi lo smartphone, visualizza gli articoli in formato griglia"
         },
         {
            "target":"#btn-view-list",
            "content": "Se usi il computer, visualizza gli articoli in formato lista"
         },
         {
            "target":"#btn-cart-previous",
            "content": "Sei un nostalgico? Torna alla precedente versione per gli acquisti"
         }
      ],

    };
  },
  components: {
    appArticleOrder: articleOrder,
    appArticleOrderList: articleOrderList,
    appSearchArticleOrders: searchArticleOrders,
    appViewArticleOrders: viewArticleOrders,
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
    // console.log('mounted route.params.order_type_id  '+this.order_type_id+' route.params.order_id  '+this.order_id);

    this.viewList = this.getCookie('viewList');

    this.getGlobals();
    this.getAjaxOrder();
    this.scroll();

    var found = this.checkCookieTour(this.scope);
    console.log('checkCookieTour found '+found+' per lo scope '+this.scope);
    if(!found)
      this.$tours['myTour'].start()
  },
  methods: {
    getGlobals() {
      /*
       * variabile che arriva da cake, dichiarata come variabile in Layout/vue.ctp, in app.js settata a window. 
       * recuperata nei components con getGlobals()
       */
      this.j_seo = window.j_seo;
    },
    totalPrice() {
      return this.$options.filters.currency(this.articles.reduce(
        (current, next) => current + (next.cart.qta_new * next.price),
        0
      ));
    },  
    onSearch: function(q) {
      this.articles = [];
      this.page = 1;
      this.q = q;
      this.scroll();
      this.isScrollFinish = false;
      // console.log('onSearch '+q);
    },
    onChangeView: function(viewList) {
      this.viewList = viewList;
      console.log('onChangeView '+this.viewList);
    },      
    scroll() {

      // console.log('scroll page '+this.page+' isRunArticles '+this.isRunArticles+' isScrollFinish '+this.isScrollFinish);
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
        q: this.q
      };
      // console.log('getsAjaxArticles url '+url)

      axios
        .post(url, params)
        .then(response => {

          this.isRunArticles = false;

          // console.log('getsAjaxArticles');
          // console.log(response.data);
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
            this.isScrollFinish = true;
          }

        })
        .catch(error => {
          this.isRunArticles = false;
          console.error("Error: " + error);
          this.isScrollFinish = true;
        });
    },




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
      console.log("addCookie scope corrente " + value);
      console.log("addCookie oldCookies value " + value_old);

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
              console.log(value_olds[i]);
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
        console.log("addCookie newCookies value " + value_new);
        this.setCookie(name, value_new, 365);
      }
      else {
        console.log("addCookie value gia' presente ");
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
      console.log('checkCookieTour get cookie_name '+this.cookie_name);
      if(values=='')
        console.log('not found cookie_name '+this.cookie_name+' => eseguo tour');
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
    onStart() {
      console.log('onStart');
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
</style>