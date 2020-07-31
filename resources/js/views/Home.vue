<template>

    <div class="card-columns card-deck-disabled">
      <div class="card"   
          v-for="articles_order in articles_orders"
          :article="articles_order"
          :key="articles_order.article_id"
        >

          <app-articles-orders
            v-bind:articles_order="articles_order"></app-articles-orders>
      </div>
    </div>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import articlesOrders from "../components/part/ArticlesOrders.vue";

export default {
  name: "Home",
  data() {
    return {
      order_id: 20022,
      articles_orders: Object,
      article_variant_item_selected: 2,
      selected: "A",
      counter: 0,
      fromChild: "", // This value is set to the value emitted by the child
      displayList: false
    };
  },
  components: {
    appArticlesOrders: articlesOrders,
  },  
  mounted() {
    this.getsArticlesOrders();
  },
  methods: {
    onIncrementEmit() {
      this.counter++;
      console.log(this.counter);
    },
    // Triggered when `childToParent` event is emitted by the child.
    onChildClick(value) {
      this.fromChild = value;
    },
    getsArticlesOrders() {
      let params = {
        order_id: this.order_id
      };
      axios
        .post("http://neo.portalgas.local.it:81/api/articles-orders/getCartsByOrder", params)
        .then(response => {
          // console.log(response.data);
          this.articles_orders = response.data;
          console.log(this.articles_orders);
        })
        .catch(error => {
          console.log("Error: " + error);
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

<style>
</style>