<template>
  
  <div>

      <modal-component></modal-component>
 
      <transition name="leave">
        <router-view></router-view>
      </transition>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import modal from './components/part/Modal';

export default {
  name: "app",
  components: {
    modalComponent: modal,
  },
};
</script>

<style>
</style>