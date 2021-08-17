<template>
  <main>
    <h1>Cart {{ article_id }} {{ msg }}</h1>
    <button type="button" name="button" v-on:click="$emit('onIncrementEmit')">
      Click me to increment!
    </button>

    <!-- set a variable then trigger a method which calls `$emit()` -->
    <label for="child-input">Child input: </label>
    <input id="child-input" type="text" name="msg" v-model="childMessage" />
  </main>

  <!-- v-on:keyup="emitToParent" -->
</template>

<script>
export default {
  name: "Cart",
  props: {
    article_id: Number,
    options: Array,
    msg: null,
    childMessage: null
  },
  mounted() {
    // this.msg = this.options[0].text;
  },
  methods: {
    // Define the method that emits data to the parent as the first parameter to `$emit()`.
    // This is referenced in the <template> call in the parent. The second parameter is the payload.
    emitToParent() {
      this.$emit("childToParent", this.childMessage);
    }
  }
};
</script>

<style scoped>
h1 {
  margin: 25px;
  border: 1px solid blue;
}
</style>