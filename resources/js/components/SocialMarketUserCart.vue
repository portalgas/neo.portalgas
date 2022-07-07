<template>

  <main id="accordion-orders">

    <h2>
      SocialMarket
    </h2>

    <div class="card"
         v-for="(order, index) in datas"
         :order="order"
         :key="index"
         :class="'type-'+order.order_type.name"
    >

      <div class="card-header" data-toggle="collapse" :data-target="'#'+order.id" aria-expanded="true" :aria-controls="'collapse-'+order.id" v-on:click="selectOrder(order)">
        <i :id="'fas-'+order.id" class="fas fa-angle-down float-right" aria-hidden="true"></i>

        <div class="content-img-supplier">
          <img v-if="order.suppliers_organization.supplier.img1 != ''"
               class="img-supplier"
               :src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
               :alt="order.suppliers_organization.name">
        </div>

        {{ order.suppliers_organization.name }}

      </div>

      <div :id="'collapse-'+order.id" class="collapse" :aria-labelledby="'heading-'+order.id" data-parent="#accordion-orders">
        <div class="card-body">

          <div v-if="isRun" class="box-spinner">
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>

          <!-- ORDERS -->
          <social-market-user-cart-orders v-if="!isRun" :results="results"></social-market-user-cart-orders>

        </div> <!-- card-body -->
      </div>

    </div>

  </main>

</template>

<script>
import { mapActions } from "vuex";
import SocialMarketUserCartOrders from '../components/SocialMarketUserCartOrders.vue';

export default {
  name: "social-market_user-cart",
  components: {
    SocialMarketUserCartOrders
  },
  data() {
    return {
      orders: {},
      isRun: false,
      results: {
        order_id: null,
        orders: []
      },
    };
  },
  props: {
    datas: {}
  },
  methods: {
    selectOrder(order) {
      console.log(order, 'selectOrder ');

      let isOpen = $('#collapse-' + order.id).hasClass('show');

      $('.collapse').removeClass('show');
      $('#accordion-orders .fas').removeClass("fa-angle-up");
      $('#accordion-orders .fas').addClass("fa-angle-down");

      if (!isOpen) {
        // console.log('Tab chiuso => lo apro ');
        $('#collapse-' + order.id).addClass('show');
        $('#accordion-orders #fas-' + order.id).addClass("fa-angle-up");
      } else {
        // console.log('Tab aperto => esco ');
        return;
      }

      this.isRun = true;

      let params = {
        delivery_id: order.delivery_id
      };
console.log(params);
      this.orders = [];

      let url_orders = "/admin/api/orders/user-cart-gets/9";
      axios
          .post(url_orders, params)
          .then(response => {

            this.isRun = false;

            /* console.log(response.data); */
            if (typeof response.data !== "undefined") {
              var data = {
                order_id: order.id,
                orders: response.data
              }
              this.results = data;
               console.log(this.results);
            }
          })
          .catch(error => {

            this.isRun = false;

            console.error("Error: " + error);
          });
    }
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
.card {
  border: none;
}
.card-header {
  cursor: pointer;
  color: #0a659e;
  font-weight: normal;
}
.card-header:hover {
  color: #fa824f;
}
</style> 