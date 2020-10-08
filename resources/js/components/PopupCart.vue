<template>
  <div class="box">
    <span v-if="!hasProduct()">No products :/</span>
    <div
      v-for="(cart, index) in getArticlesInCart"
      :key="index"
      class="box-item"
    >
      <img :src="cart.article.img1" alt="" class="item-thumb" />
      <h3 class="item-name">{{ cart.article.name }}</h3>
      <span class="item-amount">Qty: {{ cart.qty }}</span>
      <span class="item-price">{{ cart.article.price }} &euro;</span>
      <span class="item-price">{{ cart.qty * cart.article.price }} &euro;</span>
    </div>
    <div class="cart-info" v-if="hasProduct()">
      <span>Totale: {{ totalPrice() }} &euro;</span>
      <router-link to="/checkout">
        <app-btn-cart
          btnClass="btn btn-small btn-info"
          @click.native="showPopupCart()"
        >
          View cart
        </app-btn-cart>
      </router-link>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import btnCart from "../components/common/BtnCart";

export default {
  components: {
    appBtnCart: btnCart
  },
  computed: {
    ...mapGetters(["getArticlesInCart"])
  },
  methods: {
    ...mapActions(["showOrHiddenPopupCart"]),
    hasProduct() {
      return this.getArticlesInCart.length > 0;
    },
    totalPrice() {
      return this.getArticlesInCart.reduce(
        (current, next) => current + (next.qty * next.article.price),
        0
      );
    },
    showPopupCart() {
      this.showOrHiddenPopupCart();
    }
  }
};
</script>

<style scoped>
.box {
  width: 400px;
  height: auto;
  background-color: #fafafa;
  box-shadow: 0px 0px 10px rgba(73, 74, 78, 0.1);
  border-radius: 5px;
  box-sizing: border-box;
  padding: 1em 0.5em;
  position: absolute;
  z-index: 1;
}

.box:after {
  content: "";
  width: 30px;
  height: 30px;
  transform: rotate(45deg);
  background: inherit;
  position: absolute;
  top: -15px;
  right: 15px;
}

.box-item {
  width: 100%;
  height: 130px;
  background-color: #fff;
  box-sizing: border-box;
  border-radius: 3px;
  padding: 0 0.5em;
  margin-top: 0.3em;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(3, 1fr);
}

.item-thumb {
  max-width: 70%;
  grid-column: 1/2;
  grid-row: 1/4;
  align-self: center;
}

.item-name {
  grid-column: 2/4;
  grid-row: 1/2;
  font-weight: normal;
}

.item-amount {
  grid-column: 2/3;
  grid-row: 2/4;
  color: #ddd;
}

.cart-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
