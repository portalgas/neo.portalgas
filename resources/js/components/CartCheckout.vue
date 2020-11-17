<template>
  <div class="container table-responsive">
    <table id="cart" class="table table-hover table-sm">
      <thead>
        <tr>
          <th style="width:50%">Product</th>
          <th style="width:10%">Price</th>
          <th style="width:8%">Quantità</th>
          <th style="width:22%" class="text-center">Subtotal</th>
          <th style="width:10%"></th>
        </tr>
      </thead>

      <!-- transition-group name="list-shopping-cart" tag="tbody" -->
        <app-cart-item
          v-for="cartItem in getArticlesInCart"
          :cartItem="cartItem"
          :key="cartItem.id"
        ></app-cart-item>
      <!-- /transition-group -->

      <tfoot>
        <tr class="d-table-row d-sm-none">
          <td class="text-center">
            <strong>Totale {{ cartTotal }} &euro;</strong>
          </td>
        </tr>
        <tr>
          <td>
            <button class="btn btn-warning" href="#" @click="saveShoppingCartLocal">
              <i class="fa fa-angle-left"></i> Save and Continue Shopping
            </button>
          </td>
          <td colspan="2" class="d-none d-sm-table-cell"></td>
          <td class="d-none d-sm-table-cell text-center">
            <strong>Totale {{ cartTotal }} &euro;</strong>
          </td>
          <td class="px-0">
            <button class="btn btn-success" href="#" @click="checkout">
              <span class="text-nowrap">
                Checkout
                <i class="fa fa-angle-right d-inline"></i>
              </span>
            </button>
          </td>
        </tr>
      </tfoot>
    </table>

    {{ getArticlesInCart }}
    
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import CartItem from "./CartCheckoutItem.vue";

export default {
  computed: {
    ...mapGetters(["getArticlesInCart", "cartTotal"])
  },
  components: {
    appCartItem: CartItem
  },
  methods: {
    ...mapActions(["addMessage"]),
    hasProduct() {
      return this.getArticlesInCart.length > 0;
    },
    totalPrice() {
      return this.getArticlesInCart.reduce(
        (current, next) => current + next.article.price,
        0
      );
    },
    remove(index) {
      this.removeProduct(index);
    },
    checkout() {},
    checkValidCart(itemList, prodList) {
      let isValid = true;
      let message = "";

      itemList.map(item => {
        for (let prodIdx = 0; prodIdx < prodList.length; prodIdx++) {
          if (prodList[prodIdx].id == item.id) {
            if (prodList[prodIdx].qta < item.qta) {
              message = `Only ${prodList[prodIdx].qta} ${item.name} available in stock`;
              isValid = false;
              return;
            }
            break;
          }
        }
      });
      return {
        isValid,
        message
      };
    },
    saveShoppingCartLocal() {
      console.log("saveShoppingCartLocal");
      /*
      let { isValid, message } = this.checkValidCart(
        this.cartItemList,
        this.products
      );
      */
      let _this = this;
      let isValid = true;

      let params = {
        carts: this.getArticlesInCart
      };
      axios
        .post("/admin/api/carts/managementCart", params)
        .then(response => {
          this.addMessage({
            messageClass: "success",
            message: "Your shopping cart is saved successfully"
          });        
          console.log(response.data);
          this.$router.push("/");
        })
        .catch(error => {
          console.error("Error: " + error);
          this.addMessage({
            messageClass: "danger",
            message: error
          });          
        });

      /*
      if (isValid) {
        this.saveShoppingCart({
          cartItemList: this.cartItemList
        }).then(() => {
          this.addMessage({
            messageClass: "success",
            message: "Your shopping cart is saved successfully"
          });
          this.$router.push("/");
        });
      } else {
        this.addMessage({
          messageClass: "danger",
          message: message
        });
      }
      */
    }
  }
};
</script>

<style scoped>
.checkout-box {
  width: 100%;
  max-width: 900px;
  display: flex;
  flex-direction: column;
  margin: 50px auto;
  box-sizing: border-box;
  padding: 1em;
}

.checkout-list {
  padding: 0;
}

.checkout-product {
  display: grid;
  grid-template-columns: 1fr 3fr 2fr 0.5fr;
  background-color: #fff;
  box-shadow: 0px 0px 10px rgba(73, 74, 78, 0.1);
  border-radius: 5px;
  list-style: none;
  box-sizing: border-box;
  padding: 0.8em;
  margin: 1em 0;
}

.checkout-product * {
  place-self: center;
}
.product-image {
  grid-column: 1/2;
  width: 50%;
}

.product-name {
  box-sizing: border-box;
}

.product-price {
  font-size: 1.2em;
  font-weight: bold;
}

.product-remove {
  width: 25px;
  height: 25px;
  border-radius: 50%;
  border: 0;
  background-color: #e0e0e0;
  color: #fff;
  cursor: pointer;
}

.total {
  font-size: 2em;
  font-weight: bold;
  align-self: flex-end;
}

.checkout-message {
  font-size: 1.5em;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s;
}

.fade-enter,
.fade-leave-to {
  transform: translateX(-40px);
  opacity: 0;
}
</style>
