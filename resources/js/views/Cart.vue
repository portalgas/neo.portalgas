<template>

    <tabs>
      <tab name="Ordini per consegna" url="/admin/api/deliveries/gets" :selected="true">
        <h2>Ordini per consegna</h2>
      </tab>
      <tab name="Ordini per produttore" url="/admin/api/orders/gets">
        <h2>Ordini per produttore</h2>
      </tab>
    </tabs>

</template>

<script>
import axios from "axios";
import Tabs from "../components/part/Tabs.vue";
import Tab from "../components/part/Tab.vue";
import OrdersDeliveries from '../components/OrdersDeliveries.vue';
import OrdersSuppliers from '../components/OrdersSuppliers.vue';

export default {
  name: "Cart",
  data() {
    return {
      displayList: false
    };
  },
  components: {
    Tabs,
    Tab,
    OrdersDeliveries,
    OrdersSuppliers
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