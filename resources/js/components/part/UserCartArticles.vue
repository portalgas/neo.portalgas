<template>

    <div>

      <div class="row ">
        <div class="header col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="header col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">Conf.</div>
        <div class="header col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">Prezzo</div>
        <div class="header col-sm-2 col-md-2 col-lg-2 col-xs-2 d-none d-md-block d-lg-block d-xl-block">Prezzo/UM</div>
        <div class="header          col-md-4 col-lg-3 col-xs-3 d-none d-md-block d-lg-block d-xl-block"></div>
      </div>

      <user-cart-article 

        @evChangeCart="changeCart"

        v-for="article in article_orders"
        v-bind:article="article"
        v-bind:order="order"
        :key="article.id">
      </user-cart-article> 

      <div class="row" v-if="order.trasport!=0.00 && order.summary_order_trasport!=null && order.summary_order_trasport.importo_trasport!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Trasporto: {{ order.summary_order_trasport.importo_trasport }} &euro;</div>
      </div>
      <div class="row" v-if="order.cost_more!=0.00 && order.summary_order_cost_more!=null && order.summary_order_cost_more.importo_cost_more!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Spesa aggiuntiva: {{ order.summary_order_cost_more.importo_cost_more }} &euro;</div>
      </div>
      <div class="row" v-if="order.cost_less!=0.00 && order.summary_order_cost_less!=null && order.summary_order_cost_less.importo_cost_less!=null">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Sconto: {{ order.summary_order_cost_less.importo_cost_less }} &euro;</div>
      </div> 
     
      <div class="row">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Totale: {{ subTotalPrice() }} &euro; </div>
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
    /*
     * event emit da btnCardAdd
     */
    changeCart: function() {
      // this.subTotalPrice();
    },    
    subTotalPrice() {
      var totale = 0;
      this.article_orders.forEach(function (article_order, index2) { 
        // console.log(article_order); 

        if(article_order.isOpenToPurchasable)  /* aperto per acquistare */
          totale += (article_order.cart.qta_new * article_order.price);
        else {
          /* ordine chiuso agli acquisti */
          totale = (totale + parseFloat(article_order.cart.final_price));               
        }

      // totale = parseFloat(totale).toFixed(2);
      }); /* loop article_orders */


      // totale = totale.replace(',', '.');

      // console.log('subTotalPrice() totale '+totale);
     
      if(this.order.trasport!=0.00 && this.order.summary_order_trasport!=null && this.order.summary_order_trasport.importo_trasport!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_trasport.importo_trasport));
      
      if(this.order.cost_more!=0.00 && this.order.summary_order_cost_more!=null && this.order.summary_order_cost_more.importo_cost_more!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_cost_more.importo_cost_more));
      
      if(this.order.cost_less!=0.00 && this.order.summary_order_cost_less!=null && this.order.summary_order_cost_less.importo_cost_less!=null)
        totale = (parseFloat(totale) + parseFloat(this.order.summary_order_cost_less.importo_cost_less));

      // console.log('subTotalPrice() totale '+parseFloat(totale));

      return this.$options.filters.currency(totale);
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
