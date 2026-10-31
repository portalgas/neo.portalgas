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

      <div class="row">
          <div class="col-10 col-sm-10 col-md-10 col-lg-9 col-xs-10">
            <order
                v-if="order_type_id!=null"
                :order_type_id="order_type_id"
                :order_id="order_id" 
            ></order>            
          </div>
          <div class="col-2 col-sm-2 col-md-2 col-lg-3 col-xs-2">
            <div class="box-btn-pdf">
                <div class="btn-group" role="group">
                  <button id="btnGroupDropCart" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-pdf"></i> Stampa carrello dell'ordine suddiviso
                  </button>
                  <div class="dropdown-menu" aria-labelledby="btnGroupDropCart">
                    <a :href="'/admin/api/exports/user-cart-splits-by-families/'+order_type_id+'/'+order_id" target="_blank" title="Stampa carrello dell'ordine suddiviso" class="dropdown-item">Per famiglia/amico</a>
                    <a :href="'/admin/api/exports/user-cart-splits-by-articles/'+order_type_id+'/'+order_id" target="_blank" title="Stampa carrello dell'ordine suddiviso" class="dropdown-item">Per articolo</a>
                </div>                
              </div>               
            </div>
          </div>
      </div>
      
      <div class="alert alert-info mt-2">
        Suddividi le quantità acquistate per le famiglie/amici che hanno partecipato all'acquisto
        <div class="beta-ribbon">Versione beta</div>
      </div>
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
.beta-alert {
  /* Assicura che l'overflow sia nascosto così la scritta non sborda fuori dall'alert */
  overflow: hidden;
}

.beta-ribbon {
  position: absolute;
  top: 10px;
  right: -25px;
  transform: rotate(35deg); /* Inquina la scritta della rotazione desiderata */
  
  background-color: #ffc107; /* Giallo warning di Bootstrap (o un rosso/arancione) */
  color: #000;
  font-size: 0.7rem;
  font-weight: normal;
  text-transform: uppercase;
  text-align: center;
  width: 100px;
  padding: 3px 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  pointer-events: none; /* Evita che interferisca con i click */
}
</style>