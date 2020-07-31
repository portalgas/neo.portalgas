<template>

  <div>
        <div class="card-header bg-primary">{{ articles_order.name }}</div>
        <img class="card-img-top" src="http://bootstrap-ecommerce.com/bootstrap-ecommerce-html/images/items/1.jpg" alt="Card image" style="width:225px">
        <div class="card-body">
            <h4 class="card-title">{{ articles_order.descri }}</h4>
            <a href="#" class="card-link">Card link</a>
            <p class="card-text">
                <del v-if="articles_order.price_pre_discount != ''"
                    >{{ articles_order.price_pre_discount }} &euro;</del
                  >
                  {{ articles_order.prezzo }} &euro;
                  <small class="text-muted"> $315.20 each </small>
            </p>
            <a href="#" class="btn btn-primary">See Profile</a>
        </div>

        <div class="card-body text-center">
            <p class="card-text">{{ articles_order.name }}</p>
        </div>
        <div class="card-footer">
           <app-btn-cart-add v-bind:articles_order="articles_order"> </app-btn-cart-add>
        </div>
  </div>

</template>

<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-articles_order",
  props: ["articles_order"],
  data() {
    return {
      qty: 1
    };
  },
  components: {
    appBtnCartAdd: btnCartAdd
  },
  mounted() {},
  methods: {
    ...mapActions(["addArticle", "addMessage"]),
    /* 
     in BtnCartAdd
    addArticleToCart(articles_order) {
      const cart = {
        id: articles_order.id,
        articles_order: Object.assign({}, articles_order),
        qty: 1
      };

      let message = "";
      message = "Add product";
      this.addMessage({
        messageClass: "success",
        message: message
      });
      console.log(cart);
      this.addArticle(cart);
    }
    */
  },
  filters: {
    shortDescription(value) {
      if (value && value.length > 75) {
        return value.substring(0, 75) + "...";
      } else {
        return value;
      }
    }
  }
};
</script>

<style></style>
