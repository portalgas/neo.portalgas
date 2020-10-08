<template>
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8" />
      <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
      />
      <meta name="description" content="" />
      <meta name="author" content="" />
      <title>---------------------------</title>
    </head>
    <div class="container-fluid">
      <app-header></app-header>
      
      <message-component></message-component>
      <message-toast-component></message-toast-component>

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

      <app-footer></app-footer>
    </div>
  </html>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import gridLoader from "vue-spinner/src/GridLoader.vue";
import header from "./components/common/Header";
import footer from "./components/common/Footer";
import message from './components/common/Message.vue';
import messageToast from './components/common/MessageToast.vue';

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
    messageToastComponent: messageToast, 
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
  watch: {
    // eslint-disable-next-line no-unused-vars
    $route(to, from) {
      // clear alert on location change
      // this.$store.dispatch("messages/clear");
    }
  },
  created() {
    this.loadLocalPage();
  }
};
</script>

<style>
.content {
  padding-top: 80px;
}
</style>
