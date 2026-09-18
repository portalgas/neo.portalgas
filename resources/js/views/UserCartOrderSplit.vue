<template>

  <main>

    <h2>
      Carrello 
    </h2>

    <p>
      <router-link to="/user-cart" class="btn btn-primary">Torna al carrello</router-link>
    </p>
        
    <div v-if="isLoading" class="box-spinner"> 
      <div class="spinner-border text-info" role="status">
        <span class="sr-only">Loading...</span>
      </div>  
    </div>

    <div v-else>

      <order
          v-if="order_type_id!=null"
          :order_type_id="order_type_id"
          :order_id="order_id" 
      ></order>
      
      <user-cart-split-articles
        v-if="datas!=null"
          v-on:emitUpdate="emitUpdate"   
          v-on:emitSubmit="emitSubmit"   
          :datas="datas" 
      ></user-cart-split-articles>

    </div>


  </main>

</template>

<script>
import axios from "axios";
import Order from "../components/Order.vue";
import UserCartSplitArticles from "../components/part/UserCartSplitArticles.vue";

export default {
name: "user-cart-order-split",
data() {
  return {
    datas: [],
    isLoading: false,
    order_type_id: null,
    order_id: null
  };
},
components: {
  Order,
  UserCartSplitArticles
},  
mounted() {
  this.order_type_id = this.$route.params.order_type_id;
  this.order_id = this.$route.params.order_id;
  this.getCartOrder();
},  
methods: {
  getCartOrder() {

      this.isLoading=true;
      this.dataFound = null;

      let params = {
        order_type_id: this.order_type_id,
        order_id: this.order_id
				};

      // this.isLoading=true;
      let url = '/admin/api/cart-splits/getByOrder';

      axios
      .post(url, params)
      .then(response => {

        this.isLoading=false;

        // console.log(response.data);
        if(typeof response.data !== "undefined") {
          this.datas = response.data.carts;
        }
        this.isLoading=false;
      })
      .catch(error => {
        this.isLoading=false;
        console.error("Error: " + error);
      });
  },
  emitUpdate() {
      this.$emit('emitUpdate', cart);
  }, 
  emitSubmit() {
      this.getCartOrder();
  }, 
}
};
</script>

<style scoped>
</style>