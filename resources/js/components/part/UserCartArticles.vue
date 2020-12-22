<template>

    <div>

      <div class="row ">
        <div class="header col-sm-2 col-xs-2 col-md-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-sm-3 col-xs-3 col-md-4 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-sm-1 col-xs-1 col-md-1 d-none d-md-block d-lg-block d-xl-block">Prezzo</div>
        <div class="header col-sm-1 col-xs-1 col-md-1 d-none d-md-block d-lg-block d-xl-block">Conf.</div>
        <div class="header col-sm-2 col-xs-2 col-md-2 d-none d-md-block d-lg-block d-xl-block">Prezzo/UM</div>
        <div class="header          col-xs-3 col-md-2 d-none d-md-block d-lg-block d-xl-block"></div>
      </div>

      <user-cart-article 
        v-for="article in article_orders"
        v-bind:article="article"
        v-bind:order="order"
        :key="article.id">
      </user-cart-article> 

      <div class="row" v-if="order.summary_order_trasport!=null && order.summary_order_trasport.importo_trasport!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Trasporto: {{ order.summary_order_trasport.importo_trasport }} &euro;</div>
      </div>
      <div class="row" v-if="order.summary_order_cost_more!=null && order.summary_order_cost_more.importo_cost_more!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Spesa aggiuntiva: {{ order.summary_order_cost_more.importo_cost_more }} &euro;</div>
      </div>
      <div class="row" v-if="order.summary_order_cost_less!=null && order.summary_order_cost_less.importo_cost_less!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Sconto: {{ order.summary_order_cost_less.importo_cost_less }} &euro;</div>
      </div> 
     
      <div class="row">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Totale: {{ subTotalPrice() }} &euro;</div>
      </div>
    </div>

</template>

<script>
import { mapActions } from "vuex";
import UserCartArticle from "../../components/part/UserCartArticle.vue";

export default {
  name: "user-cart-articles",
  props: ['order', 'article_orders'],
  components: {
    UserCartArticle: UserCartArticle
  },
  methods: {
    subTotalPrice() {
      var _order = this.order;
      var totale = this.$options.filters.currency(this.article_orders.reduce(
        // function (current, next) { return current + (next.cart.qta_new * next.price)},
        function (current, next) { 
            var totale = 0;

            if(_order.isOpenToPurchasable) 
              totale += next.cart.final_price; 
            else {
              /* ordine chiuso agli acquisti */
              totale += (next.cart.qta_new * next.price);
            }              

            return (current + totale);     
        },
        0
      ));

      totale = totale.replace(',', '.');

      // console.log('subTotalPrice) totale '+totale);
     
      if(this.order.summary_order_trasport!=null && this.order.summary_order_trasport.importo_trasport!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_trasport.importo_trasport));
      
      if(this.order.summary_order_cost_more!=null && this.order.summary_order_cost_more.importo_cost_more!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_cost_more.importo_cost_more));
      
      if(this.order.summary_order_cost_less!=null && this.order.summary_order_cost_less.importo_cost_less!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_cost_less.importo_cost_less));

      // console.log('subTotalPrice) totale '+parseFloat(totale));

      return parseFloat(totale).toFixed(2);
    }
  },
  filters: {
      currency(amount) {
        let locale = window.navigator.userLanguage || window.navigator.language;
        const amt = Number(amount);
        return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
      }      
  } 
};
</script>

<style scoped>
.header {
  background-color: #0a659e;
  color: #fff;
}
.footer {
  background-color: #e4e4e4;
  font-weight: bold;
  color: #0a659e;
  text-align: right;  
}
</style>
