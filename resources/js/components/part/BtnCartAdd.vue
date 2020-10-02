<template>
  <div>
    getArticleInCart {{ getArticleInCart }}
    <hr />
    qty-db {{ article.cart.qty }}
    qty-store {{ cart.qty }}
    <div class="quantity buttons_added">
      <input type="button" value="-" class="minus" @click="minusCart" :disabled="cart.qty == 0" />

      <input
        type="number"
        class="form-control text-center"
        :value="cart.qty"
        :disabled="article.store === 0"
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
        article: Object,
        qty: 0
      },
      articleInCart: Object
    };
  },
  props: ["article"],
  computed: {
    ...mapGetters(["getArticleInCart"])
  },
  mounted() {
    this.getCart();
  },
  watch: {
    articleInCart: function() {
      console.log("watch articleInCart");
      // console.log(newVal);
      // console.log(oldVal);
      // this.getCart();
    }
  },
  created: function() {
    console.log("created");
  },
  methods: {
    ...mapActions(["addArticle", "addMessage"]),
    getCart() {

      console.log("getCart");
      console.log(this.article);
      console.log("this.article.ids.article_id "+this.article.ids.article_id);

      (this.cart = {
        article: this.article,
        qty: 0
      }),
        
      /*
       * cerco se tra quelli gia' acquistati c'e' l'articolo corrente
       */
      (this.articleInCart = this.getArticleInCart);
      if (this.articleInCart.length == 0) {
        console.log("nessun articolo acquistato");
      } else {
        console.log("acquistati " + this.articleInCart.length + " articoli");
        console.log(
          "cerco se articolo con ids " +
            this.article.ids.article_id +
            "... è stato acquistato per recuperare la qty"
        );

        var articleInCart = null;
        if(this.articleInCart.length>0) {
          articleInCart = this.articleInCart.find(
            element => (element.article.ids.article_id == article.ids.article_id && 
                       element.article.ids.article_organization_id == article.ids.article_organization_id && 
                       element.article.ids.order_id == article.ids.order_id && 
                       element.article.ids.organization_id == article.ids.organization_id)
            // element => (element.article.ids.equals(article.ids))   
          );
        }

        if (articleInCart !== null) {
          console.log(
            "l'articolo è stato già acquistato con qty " + articleInCart.qty
          );
          this.cart.qty = articleInCart.qty;
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
      this.addArticleToCart();
    }
  }
};
</script>

<style scoped></style>
