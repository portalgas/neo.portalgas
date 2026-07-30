<template>

  <div>

      <modal-component></modal-component>
      <cashes-user :is_guest="false"></cashes-user>

      <transition name="leave">
        <router-view></router-view>
      </transition>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import modal from './components/part/ModalArticleOrder';
import cashesUser from './components/part/CashesUser';

export default {
  name: "app",
  components: {
    modalComponent: modal,
    cashesUser: cashesUser
  }
};
</script>

<style>
.row-header {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  padding: 10px;
  background: none repeat scroll 0 0 #1e83c2;
  color: #fff;
}
</style>
