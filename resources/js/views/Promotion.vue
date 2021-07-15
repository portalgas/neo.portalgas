<template>

<div>

    <div v-if="isRunPromotion" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>  
    </div>

    <div  v-if="!isRunPromotion && promotions!=null"
            v-for="(promotion, index) in promotions"
            :promotion="promotion"
            :key="promotion.promotion.id"
    >

       <div class="row">
            <div class="col-sm-12 col-xs-12 col-md-12"> 

              <div class="card mb-3">

                <div class="row no-gutters">
                  <div class="col-md-2"> 
                      <div class="content-img-supplier">
                        <img v-if="promotion.promotion.organization.suppliers_organization.supplier.img1 != ''"
                          class="img-supplier" :src="appConfig.$siteUrl+'/images/organizations/contents/'+promotion.promotion.organization.suppliers_organization.supplier.img1"
                          :alt="promotion.promotion.organization.suppliers_organization.supplier.name">
                      </div>

                  </div>
                  <div class="col-md-10">
                     <div class="card-body type-PROMOTION">
                        <h5 class="card-title">
                            <a v-if="promotion.promotion.organization.suppliers_organization.supplier.www!=''" target="_blank" v-bind:href="promotion.promotion.organization.suppliers_organization.supplier.www" title="vai al sito del produttore">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </a>
                            <span v-if="promotion.promotion.organization.suppliers_organization.supplier.www==''">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </span>
                            <small class="card-text">
                              {{ promotion.promotion.organization.suppliers_organization.supplier.descrizione }}
                            </small>                        
                        </h5>

                        <p v-if="promotion.promotion.id!=null" class="card-text">
                            <b>{{ promotion.promotion.name }}</b>
                            terminerà {{ promotion.promotion.data_fine | formatDate }}
                        </p>

                     </div> <!-- card-body -->
                     <div class="card-footer text-muted bg-transparent-disabled">
                        <strong>Note per la consegna e il pagamento</strong> 
                        <div v-html="$options.filters.html(promotion.promotion.nota)"></div>
                     </div>  <!-- card-footer --> 

                  </div> <!-- col-md-10 -->
                </div> <!-- row -->
        
              </div> <!-- card -->

            </div> <!-- col... -->
          </div> <!-- row -->

          <div class="row">

                <div class="col-sm-12 col-xs-2 col-md-3" 
                      v-for="(article, index) in promotion.article_orders"
                      :article="article"
                      :key="article.article_id"
                      > 
    
                      <div class="box-article-order" :class="{even: index % 2, odd: !(index % 2)}">
                        <app-article-order
                          v-bind:article="article"
                          v-bind:order="promotion.order">
                          </app-article-order>
                      </div>
                </div> <!-- col-sm-12 col-xs-2 col-md-3 -->
          </div> <!-- row -->

    </div> <!-- loop -->

    <div v-if="!isRunPromotion && (promotions==null || promotions.length==0)" class="alert alert-warning">
        Nessuna promozione per te
    </div>

</div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import articleOrder from "../components/part/ArticleOrder.vue";

export default {
  name: "app-promotion",
  data() {
    return {
      order: null, 
      promotions: null,
      article: null,
      isRunPromotion: false
    };
  },
  components: {
    appArticleOrder: articleOrder
  },  
  mounted() {
    this.getPromotions();
  },
  methods: {
    getPromotions() {

      this.isRunPromotion = true;

      let url = "/admin/api/promotions/gets";

      axios
        .post(url)
        .then(response => {

          this.isRunPromotion = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.promotions = response.data.results;
           }
           console.log(this.promotions);
        })
        .catch(error => {
          this.isRunPromotion = false;
          console.error("Error: " + error);
        });    
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
</style>