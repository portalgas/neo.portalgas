<template>

<main>

    <div v-if="isRunMarket" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>  
    </div>
 
    <div class="card" style="width: 18rem;" 
        v-if="!isRunMarket && markets!=null"
        v-for="(market, index) in markets"
        :market="market"
        :key="market.id"
    >
        <h5 class="card-header">{{ market.organization.suppliers_organization.supplier.name }}</h5>
        <img v-if="market.organization.suppliers_organization.supplier.img1 != ''"
          class="card-img-top"
          :src="appConfig.$siteUrl+'/images/organizations/contents/'+market.organization.suppliers_organization.supplier.img1"
          :alt="market.organization.suppliers_organization.supplier.name">

        <div class="card-body">
          <h5 class="card-title">{{ market.organization.suppliers_organization.supplier.descrizione }}</h5>
          <h6 class="card-subtitle mb-2 text-muted">
            {{ market.organization.suppliers_organization.supplier.localita }} ({{ market.organization.suppliers_organization.supplier.provincia }})
          </h6>
          <p class="card-text">
           
            {{ market.nota }}

            <span>
              <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModal(market.organization.suppliers_organization.supplier.id)">maggior dettaglio</a>
              
              <div v-if="isLoading" class="box-spinner"> 
                <div class="spinner-border text-info" role="status">
                  <span class="sr-only">Loading...</span>
                </div>  
              </div> 

            </span>

          </p>
          <p class="card-text">
            <small class="text-muted">
              {{ market.organization.suppliers_organization.supplier.www }}
            </small>
          </p>
        </div>
        <div class="card-footer">
          <small class="text-muted">
            <a class="btn btn-primary" v-on:click="selectMarket(market)">Acquista</a>
          </small>
        </div>

      </div> <!-- loop -->

    <div v-if="!isRunMarket && (markets==null || markets.length==0)" class="alert alert-warning">
        Nessun produttore vende i suoi prodotti 
    </div>

</main> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "app-social-market",
  data() {
    return {
      markets: null,
      isRunMarket: false,
      isLoading: false
    };
  },
  mounted() {
    this.getSocialMarkets();
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    getSocialMarkets() {

      this.isRunMarket = true;

      let url = "/api/social-market/gets";

      axios
        .post(url)
        .then(response => {

          this.isRunMarket = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.markets = response.data.results;
           }
           console.log(this.markets);
        })
        .catch(error => {
          this.isRunMarket = false;
          console.error("Error: " + error);
        });    
    },
    clickShowOrHiddenModal (supplier_id) {

      console.log('clickShowOrHiddenModal supplier_id '+supplier_id);

      this.isLoading=true;

      let params = {
        supplier_id: supplier_id
      };

      let url = "/admin/api/html-article-orders/get";
      axios
        .post(url, params)
        .then(response => {
            // console.log(response.data);
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.article.name,
                body: response.data,
                footer: ''
              }            

              this.isLoading=false;

              this.addModalContent(modalContent);
              this.showOrHiddenModal();              
            }
        })
        .catch(error => {
          this.isLoading=false;
          this.isRunDeliveries=false;
          console.error("Error: " + error);
        });
    }, 
    selectMarket(market) {
        /* console.log('selectMarket'); */
        /* console.log(market); */
      
        this.$router.push({ name: 'SocialShop', params: {market_id: market.id}})
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