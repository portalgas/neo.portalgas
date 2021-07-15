<template>

<div>

    <div v-if="isRunArticles" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>  
    </div>

    <div v-if="market!=null">

       <div class="row">
            <div class="col-sm-12 col-xs-12 col-md-12"> 

              <div class="card mb-3">

                <div class="row no-gutters">
                  <div class="col-md-2"> 
                    <div class="content-img-supplier">
                        <img v-if="market.organization.suppliers_organization.supplier.img1 != ''"
                          class="card-img-top"
                          :src="appConfig.$siteUrl+'/images/organizations/contents/'+market.organization.suppliers_organization.supplier.img1"
                          :alt="market.organization.suppliers_organization.supplier.name">
                      </div>

                  </div>
                  <div class="col-md-10">
                     <div class="card-body">
                        <h5 class="card-title">
                            {{ market.organization.suppliers_organization.supplier.name }}                     
                        </h5>
                     </div> <!-- card-body -->
                     <div class="card-footer text-muted bg-transparent-disabled">
                        <strong>Note</strong> 
                        <div v-html="$options.filters.html(market.organization.suppliers_organization.supplier.descrizione)"></div>
                     </div>  <!-- card-footer --> 

                  </div> <!-- col-md-10 -->
                </div> <!-- row -->
        
              </div> <!-- card -->

            </div> <!-- col... -->
          </div> <!-- row -->

          <div class="row">

                <div class="col-sm-12 col-xs-2 col-md-3" 
                      v-for="(market_article, index) in market.market_articles"
                      :article="market_article.article"
                      :key="market_article.article.article_id"
                      > 

                      <div class="box-article-order" :class="{even: index % 2, odd: !(index % 2)}">
                        <app-article-market
                          v-bind:article="market_article.article"
                          v-bind:order="market">
                          </app-article-market>
                      </div>
                </div> <!-- col-sm-12 col-xs-2 col-md-3 -->
          </div> <!-- row -->

    </div> <!-- v-if=market!=null -->

    <div v-if="!isRunArticles && market==null" class="alert alert-warning">
        Nessuna market aperto
    </div>

</div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import ArticleMarket from "../components/part/ArticleMarket.vue";

export default {
  name: "app-article",
  data() {
    return {
      market_id: null,
      market: null, 
      isRunArticles: false
    };
  },
  components: {
    appArticleMarket: ArticleMarket
  },  
  mounted() {
    this.market_id = this.$route.params.market_id; 
    this.getArticles();
  },
  methods: {
    getArticles() {

      this.isRunArticles = true;

      let url = "/api/social-market/getArticles/"+this.market_id;

      axios
        .get(url)
        .then(response => {

          this.isRunArticles = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.market = response.data.results;
           }
           console.log(this.market);
        })
        .catch(error => {
          this.isRunArticles = false;
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
</style>