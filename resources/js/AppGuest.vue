<template>

  <div>

      <modal-component></modal-component>
      <cashes-user></cashes-user>

      <transition name="leave">
        <router-view></router-view>
      </transition>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import modal from './components/part/ModalArticleOrder';
import cashesUser from "./components/part/CashesUser.vue";


export default {
  name: "app",
  components: {
      modalComponent: modal,
      cashesUser: cashesUser
  }
};
</script>

<style>
</style>
