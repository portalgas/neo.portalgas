<template>

  <div>
        <div class="card-header bg-primary">{{ article.name }}</div>
        <img class="card-img-top" :src="article.img1" :alt="article.name">
        <div class="card-body">
            <h4 class="card-title">{{ article.descri }}</h4>
            <a href="#" class="card-link">Card link</a>
            <p class="card-text">
                <del v-if="article.price_pre_discount != ''"
                    >{{ article.price_pre_discount }} &euro;</del
                  >
                  {{ article.price }} &euro;
                  <small class="text-muted"></small>
            </p>
            <a href="#" class="btn btn-primary">See Profile</a>
        </div>

        <div class="card-body text-center">
            <p class="card-text">conf {{ article.conf }}</p>
            <p class="card-text">bio {{ article.is_bio }}</p>
            <p class="card-text">um rif {{ article.um_rif_label }}</p>
        </div>
        <div class="card-footer">
           <app-btn-cart-add v-bind:article="article"></app-btn-cart-add>
        </div>
  </div>

</template>

<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ["article"],
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
