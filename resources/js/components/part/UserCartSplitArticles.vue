<template>

    <main>

      <div class="row">
        <div class="header col-2 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-3 col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">Conf.</div>
        <div class="header col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">Prezzo</div>
        <div class="header col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">Prezzo/UM</div>
        <div class="header col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">Quantità</div>
        <div class="header col-3 col-sm-3 col-md-2 col-lg-3 col-xs-3 d-none d-md-block d-lg-block d-xl-block">Suddividi</div>
      </div>

      <div v-for="data in datas">
        <user-cart-split-article 
          v-on:emitUpdate="onEmitUpdate"         
          v-bind:article_order="data" 
          :key="data.id">
          </user-cart-split-article> 
      </div>

      <div class="text-right">
        <button
            type="button"
            v-if="!isRun"
            class="btn btn-success"
            style="min-width:250px"
            :disabled="btnSaveIsDisabled"
            @click="save()"
          >
          <i class="fas fa-save"></i>  Salva le quantità suddivise
          </button>
          <div v-if="isRun" class="box-spinner">
              <div class="spinner-border text-info" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
          </div>          
     	</div>

    </main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartSplitArticle from "../../components/part/UserCartSplitArticle.vue";

export default {
  name: "user-cart-articles",
  props: ['datas'],
  components: {
    UserCartSplitArticle
  }, 
  data() {
    return {
      isRun: false
    };
  },  
  methods: {
    onEmitUpdate() {
    },  
    onEmitSubmit() {
      this.$emit('emitSubmit', true);
    },    
    save() {

      let _this = this;
      _this.isRun = true;
      let url = '/admin/api/cart-splits/storage';

      axios
        .post(url, this.datas)
        .then(response => {

          _this.isRun = false;
          _this.onEmitSubmit();
        })
        .catch(error => {
            console.error("Error: " + error);
            _this.isRun = false;
        });
      }
  },
  computed: {
      btnSaveIsDisabled()  {
        let ok_cart_splits = [];
        let all_cart_split_ok = true;
        
        this.datas.forEach((data, index) => {
          ok_cart_splits[index] = true;
          if(data.cart_splits.length>0) {
            let totale_qta_split = 0;
            data.cart_splits.forEach(cart_split => {
              totale_qta_split += cart_split.qta;
            });
            // console.log('qta totale suddivisi '+totale_qta_split+' cart.final_qta '+data.cart.final_qta, 'delta');
            if(totale_qta_split!=data.cart.final_qta)
            ok_cart_splits[index] = false;
          }
        });
        // console.table(ok_cart_splits, 'ok_cart_splits');
        all_cart_split_ok = ok_cart_splits.every(valore => valore === true);
        // console.table(all_cart_split_ok, 'all_cart_split_ok');

        return !all_cart_split_ok;
      },
  },  
  filters: {
      currency(amount) {
        let locale = window.navigator.userLanguage || window.navigator.language;
        locale = 'it-IT';
        const amt = Number(amount);
        return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
      }      
  } 
};
</script>

<style scoped>
.row {
  margin: 5px 0 5px 0;
  padding: 5px 0 5px 0;
}
.header {
  background-color: #0a659e;
  color: #fff;
}
</style>
