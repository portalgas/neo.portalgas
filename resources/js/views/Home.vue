<template>

    <div class="card-columns card-deck-disabled">
      <div class="card"   
          v-for="article in articles"
          :article="article"
          :key="article.article_id"
        >

          <app-articles
            v-bind:article="article"></app-articles>
      </div>
    </div>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import articles from "../components/part/Articles.vue";

export default {
  name: "Home",
  data() {
    return {
      order_id: 20022,
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
    this.getsArticles();
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
    getsArticles() {
      let params = {
        order_id: this.order_id
      };
      axios
        .post("http://neo.portalgas.local.it:81/admin/api/articles-orders/getCartsByOrder", params)
        .then(response => {
          // console.log(response.data);
          this.articles = response.data;
          console.log(this.articles);
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