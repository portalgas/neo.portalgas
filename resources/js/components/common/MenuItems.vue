<template>
  <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
    <li class="nav-item active">
      <router-link to="/" class="nav-link">Home</router-link>
    </li>
    <li class="nav-item">
      <a href="/users/login" class="nav-link">Login</a>
    </li>
  </ul>
</template>

<script>
export default {
  name: "app-menu-items"
};
</script>

<style></style>
