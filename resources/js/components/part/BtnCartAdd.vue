<template>

    <div>

        GIA' ACQUISTATO: qty {{ article.cart.qty }} qty_new {{ article.cart.qty_new }}

      <div
        v-if="message.msg"
        :class="`alert alert-${message.class}`"
      >
        {{ message.msg }}
      </div>

      <!-- RI-OPEN-VALIDATE -->      
      <div v-if="article.riopen!=null" class="riopen">

      {{ article.riopen.differenza_da_ordinare }}

          <div v-if="article.riopen.differenza_da_ordinare>1" class="alert alert-warning">
            Per completare il <strong>collo</strong> mancano {{ article.riopen.differenza_da_ordinare }} pezzi
          </div>   
          <div v-if="article.riopen.differenza_da_ordinare==1" class="alert alert-warning">
            Per completare il <strong>collo</strong> manca {{ article.riopen.differenza_da_ordinare }} pezzo
          </div>
          <div v-if="article.riopen.differenza_da_ordinare==0" class="alert alert-success">
            Collo completato
          </div>
      </div>

      <div class="quantity buttons_added">

        <input type="button" value="-" 
          class="minus" 
          @click="minusCart" 
          :disabled="btnMinusIsDisabled" />

        <input
          type="number"
          class="form-control text-center"
          :value="article.cart.qty_new"
          :disabled="article.store === 0 || isRun"
          @input="numberCart"
          min="0"
          size="4"
          inputmode="numeric"
          title="Qtà"
        />

        <input type="button" value="+" class="plus" @click="plusCart" :disabled="btnPlusIsDisabled" />

        <button v-if="!isRun"
          type="button"
          class="btn-save btn btn-success"
          :disabled="btnSaveIsDisabled"
          @click="save()"
        >      
          Save
        </button>

        <div v-if="isRun" class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>

      </div>
  
  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "btn-cart-add",
  data() {
    return {
      message: {
        class: null,
        msg: ''
      },
      isRun: false
    };
  },
  props: ["article"], 
  computed: {
      btnMinusIsDisabled() {
        return (this.isRun || this.article.cart.qty_new == 0);
      },
      btnPlusIsDisabled() {
        return (this.isRun || 
          (typeof this.article.riopen!="undefined" && this.article.riopen.differenza_da_ordinare==0));
      },
      btnSaveIsDisabled()  {
        return (this.isRun || this.article.cart.qty === this.article.cart.qty_new);
      },
  },   
  methods: {
    save() {

      this.isRun = true;

      let params = {
        article: this.article
      };
      console.log(params);

      axios
        .post("/admin/api/orders/managementCart", params)
        .then(response => {

          this.isRun = false;

          var messageClass = "";
          var msg = "";
          if(response.data.esito) {
              messageClass = "success";
              msg = response.data.msg;

              this.article.cart.qty = this.article.cart.qty_new;  // aggiorno la qty 
          }
          else {
              messageClass = "danger";
              if(response.data.msg!='')
                  msg = response.data.msg;
              else
              if(response.data.results!='')
                  msg = response.data.results;
          } 

          this.message = {
            class: messageClass, 
            msg: msg
          }

          var _this = this;
          setTimeout(function() {_this.message ={}}, 3000);

          console.log(response.data);
        })
        .catch(error => {
            console.error("Error: " + error);

            this.isRun = false;

            var messageClass = "danger";
            var message = error;

            this.message = {
              class: messageClass, 
              msg: message
            }
        });
    },
    minusCart() {

      this.message = {}

      if(this.article.cart.qty_new>0) {

        let qty_prima_di_modifica = this.article.cart.qty_new;
        console.log("minusCart() qty_prima_di_modifica "+qty_prima_di_modifica);

        this.article.cart.qty_new = (this.article.cart.qty_new - (1 * this.article.qty_multiple));
        
        if (this.article.cart.qty_new < this.article.qty_min) {
            this.article.cart.qty_new = 0
        }

        /*
         * di quanto e' aumentata rispetto a prima
         */
        let qty_incremento = (qty_prima_di_modifica - this.article.cart.qty_new);
        console.log("minusCart() qty_incremento "+qty_incremento);

        /* 
         * RI-OPEN-VALIDATE
         */
        if(typeof this.article.riopen!="undefined") {
          this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare + qty_incremento);
        }

        if(this.validitationCart()) {
         // this.article.cart.qty=this.article.cart.qty_new;  // aggiorno la qty originale   
        }
      }
      else {
        /* 
         * RI-OPEN-VALIDATE
         */
        if(typeof this.article.riopen!="undefined") {
          this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare + this.article.cart.qty);
        }      
      }
    },
    plusCart() {

      this.message = {}

      let qty_prima_di_modifica = this.article.cart.qty_new;
      console.log("plusCart() qty_prima_di_modifica "+qty_prima_di_modifica);

      this.article.cart.qty_new = (this.article.cart.qty_new + (1 * this.article.qty_multiple));

      /*
       * di quanto e' aumentata rispetto a prima
       */
      let qty_incremento = (this.article.cart.qty_new - qty_prima_di_modifica);
      console.log("plusCart() qty_incremento "+qty_incremento);
      
      /* 
       * RI-OPEN-VALIDATE
       */
      if(typeof this.article.riopen!="undefined") {
        this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare - qty_incremento);
      }

      if(this.validitationCart()) {
       // this.article.cart.qty=this.article.cart.qty_new; // aggiorno la qty originale
      }
    },
    numberCart(event) {
      console.log("numberCart " + event.target.value);
      this.article.cart.qty_new = parseInt(event.target.value);
      this.article.cart.qty = parseInt(event.target.value);
      
      this.validitationCart();
    },
    validitationCart() {

       var messageClass = "danger";
       var message = "";

       if(this.article.article_order.stato=="LOCK" && this.article.cart.qty_new>this.article.cart.qty) {
          message = "L'articolo è bloccato, non si possono aggiungere articoli";
          this.article.cart.qty_new = (this.article.cart.qty_new - (1 * this.article.qty_multiple));
       }
       else
       if(this.article.article_order.stato=="QTAMAXORDER" && this.article.cart.qty_new>this.article.cart.qty) {
          message = "Raggiunta la quantità massima che si può ordinare";
          this.article.cart.qty_new = (this.article.cart.qty_new - (1 * this.article.qty_multiple));
       }  
       else
       if(this.article.qty_massima_order>0) {
          if((this.article.qty_cart - qty_new>this.article.cart.qty + qty_new>this.article.cart.qty_new) > this.article.qty_massima_order) {
            message = "Raggiunta la quantità massima che si può ordinare";
            this.article.cart.qty_new = (this.article.cart.qty_new - (1 * this.article.qty_multiple));
          }
       }
       else
       if(this.article.qty_massima>0) {
          if(qty_new>this.article.cart.qty_new  > this.article.qty_massima) {
            message = "Raggiunta la quantità massima che si può ordinare";
            this.article.cart.qty_new = (this.article.cart.qty_new - (1 * this.article.qty_multiple));
          }
       }             

       if(message!='') {
          this.message = {
            class: messageClass, 
            msg: message
          }  

          return false;         
       }

       return true;
    }
  }
};
</script>

<style scoped>
.btn-save {
    margin-left: 15px;
}
.buttons_added {
    width: 100%;
    display: inline-flex;
}
.buttons_added .spinner-border {
    display: inline-table;
    margin: 0 5px;
}
.minus, .plus {
    display: flex;
    align-items: center;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid gray;
    border-radius: 2px;
}
</style>
