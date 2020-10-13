<template>
  
    <nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
      <a class="navbar-brand" href="#">PortAlGas</a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#collapsibleNavbar"
        aria-controls="collapsibleNavbar"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <app-menu-items></app-menu-items>
        <app-btn-cart
          btnClass="btn btn-small btn-info btn-popup"
          :cartIcon="true"
          @click.native="showPopupCart()"
        >
          Cart...
          <span class="btn-circle" v-if="hasProduct()">
            {{ numItems }} ({{ cartTotal }} &euro;)
          </span>
        </app-btn-cart>
        <transition name="appear">
          <app-popup-cart class="cart-popup" v-if="getPopupCart" />
        </transition>
        <app-mask v-if="getPopupCart" @click.native="showPopupCart()" />
      </div>
    
    <app-message-toast></app-message-toast>
    </nav>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

import menuItems from "../../components/common/MenuItems.vue";
import btnCart from "../../components/common/BtnCart.vue";
import popupCart from "../../components/PopupCart.vue";
import mask from "../../components/part/Mask.vue";
import messageToast from "../../components/common/MessageToast.vue";

export default {
  name: "app-header",
  data() {
    return {
      tot_qty: 0
    };
  },
  components: {
    appMenuItems: menuItems,
    appBtnCart: btnCart,
    appPopupCart: popupCart,
    appMask: mask,
    appMessageToast: messageToast
  },
  computed: {
    ...mapGetters(["getArticlesInCart", "getPopupCart", "cartTotal"]),
    numItems() {
      return this.getArticlesInCart.reduce((total, item) => {
        total += item.qty;
        return total;
      }, 0);
    },
    userEmail() {
      return this.isLoggedIn ? this.currentUser.email : "";
    }
  },
  methods: {
    ...mapActions(["showOrHiddenPopupCart"]),
    hasProduct() {
      return this.getArticlesInCart.length > 0;
    },
    showPopupCart() {
      this.showOrHiddenPopupCart();
    }
  }
};
</script>

<style>
.cart-popup {
  position: absolute;
  top: 75px;
  right: 18px;
}
</style>
