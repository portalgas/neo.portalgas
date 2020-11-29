<template>

    <div>

      <h2>
        Carrello
        <span class="btn-pdf">
          <a href="/admin/api/exports/pdf/index.pdf" target="_blank" title="Stampa carrello"><i class="fas fa-file-pdf"></i></a>
        </span>
      </h2>
      <div v-if="isLoading" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>  
      </div>

      <user-cart-orders v-if="isLoading==false" :datas="datas"></user-cart-orders> 

    </div>

</template>

<script>
import axios from "axios";
import UserCartOrders from '../components/UserCartOrders.vue';

export default {
  name: "Home",
  data() {
    return {
      datas:  {},
      isLoading: false, 
    };
  },
  components: {
    UserCartOrders
  },  
  mounted() {
    this.getDeliveries();
  },  
  methods: {
    getDeliveries() {

        // this.isLoading=true;
        let url = '/admin/api/deliveries/user-cart-gets';

        axios
        .post(url)
        .then(response => {

          this.isLoading=false;

          console.log(response.data);
          if(typeof response.data !== "undefined") {
            this.datas = response.data;
            // console.log(this.datas);                    
          }
        })
        .catch(error => {
          this.isLoading=false;
          console.error("Error: " + error);
        });
    }    
  }
};
</script>

<style scoped>
.btn-pdf {
  float: right;
}
.btn-pdf i {
  color: #fff;
}
</style>