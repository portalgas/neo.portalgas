<template>

    <div>
      <h2>Ordini aperti</h2>
      <tab :name="'Ordini per produttore'"
           :url="'/admin/api/orders/gets/socialmarket'"
           :selected="true"></tab>
    </div>

</template>

<script>
import Tab from "../components/part/Tab.vue";

export default {
  name: "SocialMarketOrders",
  components: {
    Tab
  }
};
</script>

<style scoped>
</style>