<template>

    <main>

        <!--
            GIA' ACQUISTATO: qta {{ article.cart.qta }} qta_new {{ article.cart.qta_new }} order_state_code {{ order.order_state_code.code }}
            qta_cart {{ article.qta_cart }} qta_massima_order {{ article.qta_massima_order }} stato {{ article.stato }}
        -->

      <div
          style="margin: 5px 15px;"
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
            type="text"
            class="form-control text-center"
            :value="qta_new"
            :disabled="true"
            @input="numberCart"
            min="0"
            size="4"
            inputmode="numeric"
            title="Quantità"
          />

          <input type="button" value="+" class="plus" @click="plusCart" :disabled="btnPlusIsDisabled" />

          <input
            type="text"
            class="form-control text-center text-totale"
            :value="total"
            :disabled="true"
            title="Totale"
          />

          <button v-if="!isRun"
            type="button"
            class="btn-save btn btn-success"
            :disabled="btnSaveIsDisabled"
            @click="save()"
          >
            Salva
          </button>

          <div v-if="isRun" class="box-spinner">
              <div class="spinner-border text-info" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
          </div>

        </div>
      </div>

       <div class="quantity buttons_added"
            v-if="order.order_state_code.code!='RI-OPEN-VALIDATE' && order.order_state_code.code!='OPEN'">

          <input
            type="number"
            class="form-control text-center"
            :value="qta_new_not_to_purchasable"
            :disabled="true"
            inputmode="numeric"
            title="Quantità"
          />

          <input
            type="text"
            class="form-control text-center text-totale"
            :value="total"
            :disabled="true"
            title="Totale"
          />
       </div>

  </main>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "btn-cart-add",
  data() {
    return {
      debug: false,
      message: {
        class: null,
        msg: ''
      },
      isRun: false
    };
  },
  props: ['order', 'article', 'is_social_market'],
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
      total() {
        var totale = 0;

        if(this.article.isOpenToPurchasable)  /* aperto per acquistare */
           totale = (this.article.cart.qta_new * this.article.price);
        else {
           /* ordine chiuso agli acquisti */
           totale = (totale + parseFloat(this.article.cart.final_price));
        }

        return this.$options.filters.currency(totale)+" €";
      },
      qta_new() {
        /*
         * ordine per acquistare article.isOpenToPurchasable
         */
        var qta_new = 0;

        qta_new = this.article.cart.qta_new;

        return qta_new;
      },
      qta_new_not_to_purchasable() {
        /*
         * ordine chiuso agli acquisti !article.isOpenToPurchasable
         */
        var qta_new = 0;

        if(this.article.cart.final_qta!=null)
            qta_new = this.article.cart.final_qta;
        else
            qta_new = this.article.cart.qta_new;

        return qta_new;
      }
  },
  methods: {
    ...mapActions(["cashesUserReload"]),
    emitCartSave() {
      // console.log('emitCartSave', 'BtnCartAdd');
      this.$emit('emitCartSave', true);
    },
    save() {

      this.isRun = true;

      let params = {
        article: this.article,
        order: this.order,
        order_type_id: this.order.order_type_id,
      };
      // console.log(params);

      var url = '/admin/api/carts/managementCart';
      if(this.order.order_type.code == 'PROMOTION_GAS_USERS')
         url = '/admin/api/promotion-carts/managementCart';
      if(this.is_social_market) url += '/is_social_market';

      axios
        .post(url, params)
        .then(response => {

          this.isRun = false;

          var messageClass = "";
          var msg = "";
          if(response.data.esito) {
              messageClass = "success";
              msg = response.data.msg;

              /*
               * aggiorno dati articolo
               */
              this.article.cart.qta = this.article.cart.qta_new;  // aggiorno la qta
              if(typeof response.data.results !=='undefined') {
                  let article = response.data.results;  // e' articles_orders
                  this.article.qta_cart = article.qta_cart;
                  this.article.qta_massima_order = article.qta_massima_order;
                  this.article.qta_minima_order = article.qta_minima_order;
                  this.article.stato = article.stato
              }

              this.cashesUserReload();  // cambio lo state cosi' ricarico i msg della cassa

              this.emitCartSave();
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

          // console.log(response.data);
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
        if(this.debug) console.log("minusCart() qta_prima_di_modifica "+qta_prima_di_modifica);

        this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));

        if (this.article.cart.qta_new < this.article.qta_minima) {
            this.article.cart.qta_new = 0;
        }

        /*
         * di quanto e' aumentata rispetto a prima
         */
        let qta_incremento = (qta_prima_di_modifica - this.article.cart.qta_new);
        if(this.debug) console.log("minusCart() qta_incremento "+qta_incremento);

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

      /*
       * richiamo userCartArticles::changeCart per ricalcolo subTotalPrice()
       * this.$parent.$emit('evChangeCart', true);
       */
    },
    plusCart() {

      this.message = {}

      let qta_prima_di_modifica = this.article.cart.qta_new;
      if(this.debug) console.log("plusCart() qta_prima_di_modifica "+qta_prima_di_modifica);

      this.article.cart.qta_new = (this.article.cart.qta_new + (1 * this.article.qta_multipli));

      /*
       * di quanto e' aumentata rispetto a prima
       */
      let qta_incremento = (this.article.cart.qta_new - qta_prima_di_modifica);
      if(this.debug) console.log("plusCart() qta_incremento "+qta_incremento);

      /*
       * RI-OPEN-VALIDATE
       */
      if(typeof this.article.riopen!="undefined") {
        this.article.riopen.differenza_da_ordinare = (this.article.riopen.differenza_da_ordinare - qta_incremento);
      }

      if(this.validitationCart()) {
       // this.article.cart.qta=this.article.cart.qta_new; // aggiorno la qta originale
      }

      /*
       * richiamo userCartArticles::changeCart per ricalcolo subTotalPrice()
       * this.$parent.$emit('evChangeCart', true);
       */
    },
    numberCart(event) {
      // console.log("numberCart " + event.target.value);
      this.article.cart.qta_new = parseInt(event.target.value);
      this.article.cart.qta = parseInt(event.target.value);

      this.validitationCart();
    },
    validitationCart() {

       var messageClass = "danger";
       var message = "";

       if(this.debug) console.log("validitationCart() article.stato "+this.article.stato);
       if(this.debug) console.log("validitationCart() article.cart.qta_new "+this.article.cart.qta_new);
       if(this.debug) console.log("validitationCart() article.cart.qta "+this.article.cart.qta);
       if(this.debug) console.log("validitationCart() article.qta_massima_order "+this.article.qta_massima_order);

       if(this.article.stato=="LOCK" && this.article.cart.qta_new > this.article.cart.qta) {
          message = "L'articolo è bloccato, non si possono aggiungere articoli";
          this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
       }
       else
       if(this.article.stato=="QTAMAXORDER" && this.article.cart.qta_new > this.article.cart.qta) {
          if(this.debug) console.log("A) "+this.article.stato+" cart.qta_new "+this.article.cart.qta_new+" cart.qta "+this.article.cart.qta);
          message = "Raggiunta la quantità massima ("+this.article.qta_massima_order+") che si può ordinare";
          this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
       }
       else
       if(this.article.qta_massima_order>0) {
          if((this.article.qta_cart - this.article.cart.qta + this.article.cart.qta_new) > this.article.qta_massima_order) {
            if(this.debug) console.log("B) qta_massima_order "+this.article.qta_massima_order+" cart.qta_new "+this.article.cart.qta_new+" cart.qta "+this.article.cart.qta+" article.qta_cart "+this.article.qta_cart);
            message = "Raggiunta la quantità massima ("+this.article.qta_massima_order+") che si può ordinare";
            this.article.cart.qta_new = (this.article.cart.qta_new - (1 * this.article.qta_multipli));
          }
       }
       else
       if(this.article.qta_massima>0) {
          if(this.article.cart.qta_new  > this.article.qta_massima) {
            if(this.debug) console.log("C) qta_massima "+this.article.qta_massima+" cart.qta_new "+this.article.cart.qta_new);
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
.text-totale {
    background-color: #fff3ba !important;
    max-width: 40%;
}
.btn-save {
    /* margin-left: 15px; */
}
.buttons_added {
    width: 100%;
    display: inline-flex;
}
.box-spinner {
    margin: 0px;
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
