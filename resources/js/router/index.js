import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home.vue";
import Order from "../views/Order";
import Cart from "../views/Cart.vue";
import UserCart from "../views/UserCart.vue";
import Promotion from "../views/Promotion";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home
  },
  {
    path: "/fai-la-spesa",
    name: "Cart",
    component: Cart
  },
  {
    path: "/order/:order_type_id/:order_id",
    name: "Order",
    component: Order
  },
  {
    path: "/user-cart",
    name: "UserCart",
    component: UserCart
  },
  {
    path: "/promozioni",
    name: "Promotion",
    component: Promotion
  }
];

const router = new VueRouter({
  mode: "history",
  // base: process.env.BASE_URL,
  routes
});

export default router;

/*
 * autentication login
router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = [
    "/login",
    "/form",
    "/marchi",
    "/marchio",
    "/marchio/:id",
    "/account"
  ];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem("user");
  // console.log(loggedIn);
  // console.log("authRequired " + authRequired);
  if (authRequired && !loggedIn) {
    return next("/login");
  }

  next();
});
*/
