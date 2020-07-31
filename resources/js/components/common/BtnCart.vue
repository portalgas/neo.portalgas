<template>
  <button :class="btnClass">
    <slot></slot>
    <img v-if="cartIcon" src="../../assets/img/cart.svg" alt="Cart Icon" />
  </button>
</template>

<script>
export default {
  props: ["btnClass", "cartIcon"]
};
</script>

<style scoped>
.btn {
  border: 0;
  cursor: pointer;
  box-sizing: border-box;
  border-radius: 3px;
  color: #fff;
  display: flex;
  justify-content: space-around;
  position: relative;
}

.btn-small {
  padding: 0.5em 2em;
}

.btn-medium {
  padding: 0.7em 4.5em;
}

.btn-large {
  padding: 0.7em 5em;
}

.btn-info {
  background-color: #2d9cdb;
}

.btn-sucess {
  background-color: #27ae60;
}

.btn-danger {
  background-color: #e74c3c;
}

.btn img {
  max-width: 20px;
  margin-left: 0.5em;
}
</style>
