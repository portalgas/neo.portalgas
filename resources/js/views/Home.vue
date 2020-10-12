<template>

  <div>

    <deliveries-component></deliveries-component>

  </div>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapActions } from "vuex";
import deliveries from '../components/Deliveries.vue';

export default {
  name: "Home",
  data() {
    return {
      counter: 0,
      displayList: false
    };
  },
  components: {
    deliveriesComponent: deliveries,
  },  
  mounted() {
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
    changeDisplay(isList) {
      this.displayList = isList;
    }    
  }
};
</script>

<style scoped>
</style>