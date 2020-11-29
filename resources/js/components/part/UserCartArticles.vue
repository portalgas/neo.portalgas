<template>

    <div>
      
      <div class="row">
        <div class="header col-sm-2 col-xs-2 col-md-2 d-none d-sm-block"></div>
        <div class="header col-sm-3 col-xs-3 col-md-4"></div>
        <div class="header col-sm-1 col-xs-1 col-md-1">Prezzo</div>
        <div class="header col-sm-1 col-xs-1 col-md-1">Conf.</div>
        <div class="header col-sm-2 col-xs-2 col-md-2">Prezzo/UM</div>
        <div class="header          col-xs-3 col-md-2 d-none d-sm-block"></div>
      </div>

      <user-cart-article 
        v-for="article in article_orders"
        v-bind:article="article"
        v-bind:order="order"
        :key="article.id">
      </user-cart-article> 

      <div class="row">
        <div class="footer col-sm-12 col-xs-12 col-md-12">Totale: {{ totalPrice() }} &euro;</div>
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
    totalPrice() {
      return this.$options.filters.currency(this.article_orders.reduce(
        (current, next) => current + (next.cart.qta_new * next.price),
        0
      ));
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
  background-color: #bababa;
  font-weight: bold;
  color: #0a659e;
  text-align: right;  
}
</style>
