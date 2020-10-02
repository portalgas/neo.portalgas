<template>
  <div>
    qty-db {{ articles_order.cart.qta }}
    qty-store {{ cart.qty }}
    <div class="quantity buttons_added">
      <input type="button" value="-" class="minus" @click="minusCart" :disabled="cart.qty == 0" />

      <input
        type="number"
        class="form-control text-center"
        :value="cart.qty"
        :disabled="articles_order.store === 0"
        @input="numberCart"
        min="0"
        size="4"
        inputmode="numeric"
        title="Qtà"
      />

      <input type="button" value="+" class="plus" @click="plusCart" />
    </div>

    <button
      type="button"
      class="btn btn-success"
      style="float:right;"
      :disabled="cart.qty === 0"
      @click="addArticleToCart()"
    >
      Add to cart
    </button>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "btn-cart-add",
  data() {
    return {
      cart: {
        organization_id: 0,
        articles_order_organization_id: 0,
        articles_order_id: 0,
        order_id: 0,
        articles_order: Object,
        qty: 0
      },
      articles_orderInCart: Object
    };
  },
  props: ["articles_order"],
  computed: {
    ...mapGetters(["getArticleInCart"])
  },
  mounted() {
    this.getCart();
  },
  watch: {
    articles_orderInCart: function() {
      console.log("watch articles_orderInCart");
      //  console.log(newVal);
      //  console.log(oldVal);
      // this.getCart();
    }
  },
  created: function() {
    console.log("created");
  },
  methods: {
    ...mapActions(["addArticle", "updateCart", "addMessage"]),
    getCart() {
      console.log(articles_order);

      console.log("getCart con articles_order.organization_id "+this.articles_order.organization_id+" articles_order.articles_order_organization_id " + this.articles_order.articles_order_organization_id+" articles_order.articles_order_id "+this.articles_order.articles_order_id+" articles_order.order_id "+this.articles_order.order_id);

      (this.cart = {
        organization_id: this.articles_order.organization_id,
        articles_order_organization_id: this.articles_order.articles_order_organization_id,
        articles_order_id: this.articles_order.articles_order_id,
        order_id: this.articles_order.order_id,
        articles_order: this.articles_order,
        qty: 0
      }),
        /*
         * cerco se tra quelli gia' acquistati c'e' l'articolo corrente
         */
        (this.articles_orderInCart = this.getArticleInCart);
      if (this.articles_orderInCart.length == 0) {
        console.log("nessun articolo acquistato");
      } else {
        console.log("acquistati " + this.articles_orderInCart.length + " articoli");
        console.log(
          "cerco se articolo con ids " +
            this.articles_order.articles_order_id +
            "... è stato acquistato per recuperare la qty"
        );

        var articles_orderInCart = this.articles_orderInCart.find(
          element => (element.organization_id == this.articles_order.organization_id && 
                      element.articles_order_organization_id == this.articles_order.articles_order_organization_id && 
                      element.articles_order_id == this.articles_order.articles_order_id && 
                      element.order_id == this.articles_order.order_id)
        );

        if (typeof articles_orderInCart != "undefined") {
          console.log(
            "l'articolo è stato già acquistato con qty " + articles_orderInCart.qty
          );
          this.cart.qty = articles_orderInCart.qty;
        } else {
          console.log("l'articolo NON è stato ancora acquistato => qty 0");
        }
      }
      console.log(this.cart);
    },
    addArticleToCart() {
      let message = "";
      message = "Add product";
      this.addMessage({
        messageClass: "success",
        message: message
      });
      console.log("addArticleToCart this.cart.qty " + this.cart.qty);
      console.log(this.cart);
      this.addArticle(this.cart);
    },
    minusCart() {
      if(this.cart.qty>0) {
        this.cart.qty--;
        this.addArticleToCart();      
      }
    },
    plusCart() {
      this.cart.qty++;
      this.addArticleToCart();
    },
    numberCart(event) {
      console.log("numberCart " + event.target.value);
      this.cart.qty = parseInt(event.target.value);
      // non + usato this.updateCart(this.cart);
      this.addArticleToCart();
    }
  }
};
</script>

<style scoped></style>
