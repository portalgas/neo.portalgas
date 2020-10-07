<template>

  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>
  <h1> TOAST {{ messageToast }}</h1>

</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "app-message-toast",
  mounted: function() {
    console.log("mounted app-message-toast ");
    this.$bvToast.width = "100%";
  },
  computed: {
    ...mapGetters(["getMessageToast"]),
    messageToast() {
      console.log(this.getMessageToast);
      return this.$store.state.getMessageToast;
    }
  }
};
</script>

<style scoped>
#my-toast {
  margin-top: 70px;
}
</style>
