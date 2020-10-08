<template>
  <div>
    GIA' ACQUISTATO: qty {{ article.cart.qty }} qty_new {{ article.cart.qty_new }}  {{ article.cart.article_id }}
    <hr>
    <div class="quantity buttons_added">
      <input type="button" value="-" class="minus" @click="minusCart" :disabled="article.cart.qty_new == 0" />

      <input
        type="number"
        class="form-control text-center"
        :value="article.cart.qty_new"
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
      :disabled="article.cart.qty === article.cart.qty_new"
      @click="save()"
    >
      Save
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
      articleInCart: Object,
      isSave: false
    };
  },
  props: ["article"],
  computed: {
    ...mapGetters(["getArticlesInCart"])
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
    ...mapActions(["addArticle", "addMessage", "addMessageToast", "showOrHiddenModal"]),
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
      (this.articleInCart = this.getArticlesInCart);
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
            element => (element.article.ids.article_id == this.article.ids.article_id && 
                       element.article.ids.article_organization_id == this.article.ids.article_organization_id && 
                       element.article.ids.order_id == this.article.ids.order_id && 
                       element.article.ids.organization_id == this.article.ids.organization_id)
            // element => (element.article.ids.equals(this.article.ids))   
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
    save() {
      let params = {
        article: this.article
      };
      console.log(params);

      axios
        .post("http://neo.portalgas.local.it:81/admin/api/orders-gas/managementCart", params)
        .then(response => {

          var messageClass = "";
          var message = "";
          if(response.data.esito) {
              messageClass = "success";
              message = response.data.msg;

              this.article.cart.qty = this.article.cart.qty_new;
          }
          else {
              messageClass = "danger";
              message = response.data.msg;
          }
          this.addMessage({
            messageClass: messageClass,
            message: message
          });        
          console.log(response.data);
        })
        .catch(error => {
          console.error("Error: " + error);
          this.addMessage({
            messageClass: "danger",
            message: error
          });          
        });
    },
    addArticleToCart() {
      let message = "";
      message = "Add product";
      this.addMessage({
        messageClass: "success",
        message: message
      });
      
      this.addMessageToast({
        title: "title",
        subtitle: "subtitle",
        body: message
      });
      
      console.log("addArticleToCart this.cart.qty_new " + this.cart.qty_new);
      console.log(this.cart);
      this.addArticle(this.cart);
    },
    minusCart() {
      if(this.article.cart.qty_new>0) {
        if(this.cart.qty)
          this.cart.qty--;
          
        this.article.cart.qty_new--;
        this.addArticleToCart();    
      }
    },
    plusCart() {
      this.cart.qty++;
      this.article.cart.qty_new++;
      this.addArticleToCart(); 
    },
    numberCart(event) {
      console.log("numberCart " + event.target.value);
      this.article.cart.qty_new = parseInt(event.target.value);
      this.cart.qty = parseInt(event.target.value);
      this.addArticleToCart();
    }
  }
};
</script>

<style scoped></style>
