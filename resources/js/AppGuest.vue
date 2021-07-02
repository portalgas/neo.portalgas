<template>
  
  <div>

      <modal-component></modal-component>
      <modal-component-supplier></modal-component-supplier>
 
      <transition name="leave">
        <router-view></router-view>
      </transition>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
import modal from './components/part/Modal';
import modalSupplier from './components/part/ModalSupplier';


export default {
  name: "app",
  components: {
    modalComponent: modal,
    modalComponentSupplier: modalSupplier,
  } 
};
</script>

<style>
</style>