<template>

    <div>
      
      <div class="row">
        <div class="header col-sm-2 col-xs-2 col-md-2"></div>
        <div class="header col-sm-3 col-xs-3 col-md-3"></div>
        <div class="header col-sm-1 col-xs-1 col-md-1">Prezzo</div>
        <div class="header col-sm-1 col-xs-1 col-md-1">Conf.</div>
        <div class="header col-sm-2 col-xs-2 col-md-2">Prezzo/UM</div>
        <div class="header col-sm-3 col-xs-3 col-md-3"></div>
      </div>

      <user-cart-article 
        v-for="article in article_orders"
        v-bind:article="article"
        v-bind:order="order"
        :key="article.id">
      </user-cart-article> 

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
  }
};
</script>

<style scoped>
.header {
  background-color: #0a659e;
  color: #fff;
}
</style>
