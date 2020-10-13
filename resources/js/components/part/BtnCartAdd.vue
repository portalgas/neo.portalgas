<template>

    <div>

      <!-- 
        GIA' ACQUISTATO: qty {{ article.cart.qty }} qty_new {{ article.cart.qty_new }}
      -->
      <div
        v-if="message.msg"
        :class="`alert alert-${message.class}`"
      >
        {{ message.msg }}
      </div>


      <div class="quantity buttons_added">

        <input type="button" value="-" class="minus" @click="minusCart" :disabled="article.cart.qty_new == 0 || isRun" />

        <input
          type="number"
          class="form-control text-center"
          :value="article.cart.qty_new"
          :disabled="article.store === 0 || isRun"
          @input="numberCart"
          min="0"
          size="4"
          inputmode="numeric"
          title="Qtà"
        />

        <input type="button" value="+" class="plus" @click="plusCart" :disabled="isRun" />

        <button v-if="!isRun"
          type="button"
          class="btn-save btn btn-success"
          :disabled="article.cart.qty === article.cart.qty_new"
          @click="save()"
        >      
          Save
        </button>

        <div v-if="isRun" class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>

      </div>
  
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
      message: {
        class: null,
        msg: ''
      },
      articleInCart: Object,
      isRun: false
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
    ...mapActions(["addArticle", "showOrHiddenModal"]),
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

      this.isRun = true;

      let params = {
        article: this.article
      };
      console.log(params);

      axios
        .post("/admin/api/orders/managementCart", params)
        .then(response => {

          this.isRun = false;

          var messageClass = "";
          var msg = "";
          if(response.data.esito) {
              messageClass = "success";
              msg = response.data.msg;

              this.article.cart.qty = this.article.cart.qty_new;
          }
          else {
              messageClass = "danger";
              if(response.data.msg!='')
                  msg = response.data.msg;
              else
              if(response.data.results!='')
                  msg = response.data.results;
          } 

          this.message = {
            class: messageClass, 
            msg: msg
          }

          var _this = this;
          setTimeout(function() {_this.message ={}}, 3000);

          console.log(response.data);
        })
        .catch(error => {
            console.error("Error: " + error);

            this.isRun = false;

            var messageClass = "danger";
            var message = error;

            this.message = {
              class: messageClass, 
              msg: message
            }

            var _this = this;
            setTimeout(function() {_this.message ={}}, 5000);
        });
    },
    addArticleToCart() {      
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

<style scoped>
.btn-save {
    margin-left: 15px;
}
.buttons_added {
    width: 100%;
    display: inline-flex;
}
.buttons_added .spinner-border {
    display: inline-table;
    margin: 0 5px;
}
.minus, .plus {
    display: flex;
    align-items: center;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid gray;
    border-radius: 2px;
}
</style>
