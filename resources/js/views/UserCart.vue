<template>

    <main>

      <h2>
        Carrello
      </h2>
      <div v-if="isLoading" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>  
      </div>

      <div v-if="dataFound===false" class="alert alert-warning">
        Carrello vuoto, non sono stati effettuai acquisti
      </div>

      <user-cart-deliveries v-if="isLoading==false" :datas="datas"></user-cart-deliveries>

      <social-market-user-cart v-if="isSocialMarketLoading==false" :datas="socialMarketDatas"></social-market-user-cart>

    </main>

</template>

<script>
import axios from "axios";
import UserCartDeliveries from '../components/UserCartDeliveries.vue';
import SocialMarketUserCart from '../components/SocialMarketUserCart.vue';

export default {
  name: "Home",
  data() {
    return {
      datas:  {},
      isLoading: false,
      dataFound: null,
      socialMarketDatas: {},
      isSocialMarketLoading: false
    };
  },
  components: {
    UserCartDeliveries,
    SocialMarketUserCart
  },  
  mounted() {
    this.getDeliveries();
    this.getSocialMarketSuppliers();
  },  
  methods: {
    getSocialMarketSuppliers() {
      this.isSocialMarketLoading = true;

      let url = '/admin/api/social-markets/user-cart-gets';

      axios
          .post(url)
          .then(response => {

            this.isSocialMarketLoading = false;

            // console.log(response.data);
            if(typeof response.data !== "undefined") {
              this.socialMarketDatas = response.data;
              // console.log(this.socialMarketDatas);
            }
            this.isSocialMarketLoading = false;
          })
          .catch(error => {
            this.isSocialMarketLoading=false;
            console.error("Error: " + error);
          });
    },
    getDeliveries() {

        this.isLoading=true;
        this.dataFound = null;

        // this.isLoading=true;
        let url = '/admin/api/deliveries/user-cart-gets';

        axios
        .post(url)
        .then(response => {

          this.isLoading=false;

          // console.log(response.data);
          if(typeof response.data !== "undefined") {
            this.datas = response.data;
            // console.log(this.datas);
            if(this.datas.length==0) 
              this.dataFound = false;
            else 
              this.dataFound = true;                  
          }
          this.isLoading=false;
        })
        .catch(error => {
          this.isLoading=false;
          this.dataFound = null;
          console.error("Error: " + error);
        });
    }    
  }
};
</script>

<style scoped>
</style>