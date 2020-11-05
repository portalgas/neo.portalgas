<template>

    <tabs>
      <tab name="Ordini per consegna" url="/admin/api/deliveries/gets" :selected="true">
        <h2>Consegne</h2>        
        <!-- orders-deliveries></orders-deliveries--> 
      </tab>
      <tab name="Ordini per produttore" url="/admin/api/orders/gets">
        <h2>Produttori</h2>        
        <!--orders-suppliers></orders-suppliers-->
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
  name: "Home",
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