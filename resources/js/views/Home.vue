<template>

  <div>
    <deliveries-component></deliveries-component>
  </div>

</template>

<script>
import axios from "axios";
import deliveries from '../components/Deliveries.vue';

export default {
  name: "Home",
  data() {
    return {
      displayList: false
    };
  },
  components: {
    deliveriesComponent: deliveries,
  },  
  methods: {
    changeDisplay(isList) {
      this.displayList = isList;
    }    
  }
};
</script>

<style scoped>
</style>