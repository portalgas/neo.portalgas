<template>

    <div>

        <!-- GIA' ACQUISTATO: qta {{ article.cart.qta }} qta_new {{ article.cart.qta_new }} {{ order.order_state_code.code }} -->

      <div
        v-if="message.msg"
        :class="`alert alert-${message.class}`"
      >
        {{ message.msg }}
      </div>

      <div v-if="order.order_state_code.code=='RI-OPEN-VALIDATE' || order.order_state_code.code=='OPEN'">
        <!-- RI-OPEN-VALIDATE -->      
        <div v-if="article.riopen!=null" class="riopen">

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
            :value="article.cart.qta_new"
            :disabled="true"
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

       <div v-if="order.order_state_code.code!='RI-OPEN-VALIDATE' && order.order_state_code.code!='OPEN'">
          <input
            type="number"
            class="form-control text-center"
            :value="article.cart.qta_new"
            :disabled="true"
            inputmode="numeric"
            title="Qtà"
          />        
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
  props: ['order', 'article'], 
  computed: {
      btnMinusIsDisabled() {
        return (this.isRun || this.article.cart.qta_new == 0);
      },
      btnPlusIsDisabled() {
        return (this.isRun || 
          (typeof this.article.riopen!="undefined" && this.article.riopen.differenza_da_ordinare==0));
      },
      btnSaveIsDisabled()  {
        return (this.isRun || this.article.cart.qta === this.article.cart.qta_new);
      },
  },   
  methods: {
    save() {

      this.isRun = true;

      let params = {
        article: this.article,
        order: this.order,
      };
      console.log(params);

      axios
        .post("/admin/api/carts/managementCart", params)
        .then(response => {

          this.isRun = false;

          var messageClass = "";
          var msg = "";
          if(response.data.esito) {
              messageClass = "success";
              msg = response.data.msg;

              this.article.cart.qta = this.article.cart.qta_new;  // aggiorno la qta 
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

      if(this.article.cart.qta_new>0) {

        let qta_prima_di_modifica = this.article.cart.qta_new;
        console.log("minusCart() qta_prima_di_modifica "+qta_prima_di_modifica);

        this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
        
        if (this.article.cart.qta_new < this.article.qta_minima) {
            this.article.cart.qta_new = 0;
        }

        /*
         * di quanto e' aumentata rispetto a prima
         */
        let qta_incremento = (qta_prima_di_modifica - this.article.cart.qta_new);
        console.log("minusCart() qta_incremento "+qta_incremento);

        /* 
         * RI-OPEN-VALIDATE
         */
        if(typeof this.article.riopen!="undefined") {
          this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare + qta_incremento);
        }

        if(this.validitationCart()) {
         // this.article.cart.qta=this.article.cart.qta_new;  // aggiorno la qta originale   
        }
      }
      else {
        /* 
         * RI-OPEN-VALIDATE
         */
        if(typeof this.article.riopen!="undefined") {
          this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare + this.article.cart.qta);
        }      
      }
    },
    plusCart() {

      this.message = {}

      let qta_prima_di_modifica = this.article.cart.qta_new;
      console.log("plusCart() qta_prima_di_modifica "+qta_prima_di_modifica);

      this.article.cart.qta_new = (this.article.cart.qta_new + (1 * this.article.qta_multipli));

      /*
       * di quanto e' aumentata rispetto a prima
       */
      let qta_incremento = (this.article.cart.qta_new - qta_prima_di_modifica);
      console.log("plusCart() qta_incremento "+qta_incremento);

      /* 
       * RI-OPEN-VALIDATE
       */
      if(typeof this.article.riopen!="undefined") {
        this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare - qta_incremento);
      }

      if(this.validitationCart()) {
       // this.article.cart.qta=this.article.cart.qta_new; // aggiorno la qta originale
      }
    },
    numberCart(event) {
      console.log("numberCart " + event.target.value);
      this.article.cart.qta_new = parseInt(event.target.value);
      this.article.cart.qta = parseInt(event.target.value);
      
      this.validitationCart();
    },
    validitationCart() {

       var messageClass = "danger";
       var message = "";

       if(this.article.stato=="LOCK" && this.article.cart.qta_new > this.article.cart.qta) {
          message = "L'articolo è bloccato, non si possono aggiungere articoli";
          this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
       }
       else
       if(this.article.stato=="QTAMAXORDER" && this.article.cart.qta_new > this.article.cart.qta) {
          console.log("A) "+this.article.stato+" cart.qta_new "+this.article.cart.qta_new+" cart.qta "+this.article.cart.qta);
          message = "Raggiunta la quantità massima ("+this.article.qta_massima_order+") che si può ordinare";
          this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
       }  
       else
       if(this.article.qta_massima_order>0) {
          if((this.article.qta_cart - this.article.cart.qta + this.article.cart.qta_new) > this.article.qta_massima_order) {
            console.log("B) qta_massima_order "+this.article.qta_massima_order+" cart.qta_new "+this.article.cart.qta_new+" cart.qta "+this.article.cart.qta+" article.qta_cart "+this.article.qta_cart);
            message = "Raggiunta la quantità massima ("+this.article.qta_massima_order+") che si può ordinare";
            this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
          }
       }
       else
       if(this.article.qta_massima>0) {
          if(this.article.cart.qta_new  > this.article.qta_massima) {
            console.log("C) qta_massima "+this.article.qta_massima+" cart.qta_new "+this.article.cart.qta_new);
            message = "Raggiunta la quantità massima ("+this.article.qta_massima_order+") che si può ordinare";
            this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
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
