<template>
  <b-toast id="my-toast" :variant="messageToast.class" solid>
    <template v-slot:toast-title v-if="messageToast.title">
      <div class="d-flex flex-grow-1 align-items-baseline">
        <b-img
          blank
          blank-color="#ff5555"
          class="mr-2"
          width="12"
          height="12"
        ></b-img>
        <strong class="mr-auto">{{ messageToast.title }}</strong>
        <small class="text-muted mr-2">{{ messageToast.subtitle }}</small>
      </div>
    </template>
    {{ messageToast.body }}
  </b-toast>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "app-message-toast",
  mounted: function() {
    this.$bvToast.width = "100%";
  },
  computed: {
    ...mapGetters(["messageToast"]),
    messageToast() {
      // this.$store.state.messageToast.message
      //console.log(this.messageToast);
      return this.$store.state.messageToast.message;
    }
  }
};
</script>

<style scoped></style>
