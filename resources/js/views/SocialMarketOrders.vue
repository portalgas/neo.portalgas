<template>

    <div>

      <div class="alert alert-info">
        <p>
          In questa sezione vogliamo proporre alcuni produttori già iscritti a PortAlGas che collaborano con i G.A.S.<br />
          Questo nella speranza che possiate ritenerli interessanti nel progetto che portano avanti e nei prodotti che offrono così da creare un'opportunità di contatto.
        </p>
        <p>
          Se sei interessato, prenota tramite PortAlGas i prodotti e sarai contattato dal produttori per concordare il pagamento e la consegna.
        </p>
      </div>

      <h2>Ordini aperti</h2>
      <tab :name="'Ordini per produttore'"
           :url="'/admin/api/orders/gets/9'"
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