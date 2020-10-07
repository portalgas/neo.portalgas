<template>

    <section class="message">
      <div class="col-sm-6 offset-sm-3">
        <div
          v-if="message"
          :class="`alert alert-${messageClass}`"
        >
          {{ message }}
        </div>
      </div>
    </section>

</template>

<script>
import { mapGetters } from "vuex";

export default {
  name: "app-message",
  computed: {
    ...mapGetters(["getMessage"]),
    messageClass() {
      return this.getMessage.messageClass;
    },
    message() {
      return this.getMessage.message;
    }
  }
};
</script>

<style scoped>
.message {
  margin-top: 50px;
}
</style>
