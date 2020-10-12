<template>

  <div>

    <div class="card-deck-disabled card-columns">
      <div class="card"
          v-for="article in articles"
          :article="article"
          :key="article.article_id"
        >
          <app-articles
            v-bind:article="article"></app-articles>
      </div>
    </div>

  </div>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapActions } from "vuex";
import articles from "../components/part/Articles.vue";

export default {
  name: "app-order",
  data() {
    return {
      order_id: 0,
      articles: Object,
      article_variant_item_selected: 2,
      selected: "A",
      counter: 0,
      fromChild: "", // This value is set to the value emitted by the child
      displayList: false
    };
  },
  components: {
    appArticles: articles,
  },  
  mounted() {
  	this.order_id = this.$route.params.order_id
    this.getsArticles();
  },
  created () {
    const cartArticles = JSON.parse(localStorage.getItem('cartArticles'));
    /*
      console.log("localStorage");
      console.log(cartArticles);

    if (cartArticles) {
      cartArticles.forEach(this.addArticleFromLocalStorage);     
    }
    */
  },  
  methods: {
    ...mapActions(["addArticle"]),
    addArticleFromLocalStorage(item, index) {
      console.log(index+" addArticleFromLocalStorage");
      let cart = {
        article: item.article,
        qty: item.article.cart.qty
      }

      console.log(cart);
      this.addArticle(cart);
    },
    onIncrementEmit() {
      this.counter++;
      console.log(this.counter);
    },
    // Triggered when `childToParent` event is emitted by the child.
    onChildClick(value) {
      this.fromChild = value;
    },
    getsArticles() {
      let url = "/admin/api/orders-gas/getCarts";
      // let url = "/admin/api/orders-promotion/getCarts";
      let params = {
        order_id: this.order_id
      };
      axios
        .post(url, params)
        .then(response => {
          console.log(response.data);
          if(typeof response.data[0] !== "undefined" && typeof response.data[0].ids !== "undefined") {
            this.articles = response.data;
            console.log(this.articles);
          }
        })
        .catch(error => {
          console.error("Error: " + error);
        });
    },
    cartArticleDetailsVariantItems(event) {
      console.log(
        "article_variant_item_selected " + this.article_variant_item_selected
      );

      var variant_item_id = event.target.value;
      var article_id = this.article.id;
      var data = {
        article_id: article_id,
        variant_item_id: variant_item_id
      };

      console.log(data);
    },
    changeDisplay(isList) {
      this.displayList = isList;
    }    
  }
};
</script>

<style scoped>
.card { 
  border: none;
}
</style>