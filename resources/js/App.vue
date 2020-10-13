<template>
  
  <div>

      <modal-component></modal-component>
      <message-component></message-component>

      <div class="content" :class="{ loadingItem: isProductLoading }">

        <div class="" v-if="isProductLoading">
          <grid-loader
            :loading="isProductLoading"
            :color="loaderColor"
            :size="loaderSize"
          ></grid-loader>
        </div>
        <div v-else class="">
          <transition name="leave" v-if="!isProductLoading">
            <router-view></router-view>
          </transition>
        </div>
      </div>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import gridLoader from "vue-spinner/src/GridLoader.vue";
import header from "./components/common/Header";
import footer from "./components/common/Footer";
import message from './components/common/Message.vue';
import modal from './components/part/Modal';

export default {
  name: "app",
  data() {
    return {
      loaderColor: "#5cb85c",
      loaderSize: "50px",
      name
    };
  },
  components: {
    appHeader: header,
    appFooter: footer,
    messageComponent: message, 
    modalComponent: modal,
    gridLoader
  },
  methods: {
    ...mapActions(["loadPage"]),
    loadLocalPage() {
      this.loadPage();
    }   
  },
  computed: {
    ...mapGetters(["isProductLoading"])
  },
  created() {
    this.loadLocalPage();
  }
};
</script>

<style>
</style>
