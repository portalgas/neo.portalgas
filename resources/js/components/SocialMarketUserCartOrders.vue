<template>

	<main>

    <p
    v-for="(order, index) in results.orders"
    :order="order"
    :key="order.id" class="box-order">

      <a>
        <div v-if="order.nota!=null && order.nota!=''" class="col-10 alert alert-info ml-auto mr-1"
             v-html="$options.filters.html(order.nota)">
        </div>

        <div v-if="order.mail_open_testo!=null && order.mail_open_testo!=''" class="col-10 alert alert-info ml-auto mr-1"
             v-html="$options.filters.html(order.mail_open_testo)">{{ order.mail_open_testo }}
        </div>
      </a>

      <user-cart-articles
            :order="order"
            :article_orders="order.article_orders"
            :is_social_market="true"
            ></user-cart-articles>

    </p> <!-- loop orders -->

	</main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartArticles from "../components/part/UserCartArticles.vue";

export default {
    name: "social-market-user-cart-orders",
    /*
     * results: {
     * 		order_id: null,
     *   	orders: []
     * },
     */
    props: {
      results: {}
    },
    components: {
      UserCartArticles
    },
	  filters: {
	    	currency(amount) {
		      let locale = window.navigator.userLanguage || window.navigator.language;
	          locale = 'it-IT';
		      const amt = Number(amount);
		      return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
		    },
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            locale = 'it-IT';
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        },
        counter: function (index) {
            return index+1
        },
        html(text) {
          return text;
        },
     }
};
</script>

<style scoped>
.box-order {
	clear: both;
}
</style>