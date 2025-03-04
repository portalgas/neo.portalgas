import Vue from "vue";
import VueRouter from "vue-router";
import Home from "../views/Home.vue";
import Order from "../views/Order.vue";
import Cart from "../views/Cart.vue";
import UserCart from "../views/UserCart.vue";
import Promotion from "../views/Promotion.vue";
import Suppliers from "../views/Suppliers.vue";
import Supplier from "../views/Supplier.vue";
import Gas from "../views/Gas.vue";
import GasSuppliers from "../views/GasSuppliers.vue";
import Gmaps from "../views/GoogleMapLoader.vue";
import SocialMarketOrders from "../views/SocialMarketOrders.vue";
/* import SocialMarket from "../views/SocialMarket.vue"; */
/* import SocialShop from "../views/SocialShop.vue";  */

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
    path: "/order/:order_type_id/:order_id",  // se order_type_id = 9 => is_social_market
    name: "Order",
    component: Order
  },
  {
    path: "/user-cart",
    name: "UserCart",
    component: UserCart
  },
  {
    path: "/site/produttori",
    name: "Suppliers",
    component: Suppliers
  },
  {
    path: "/site/produttore/:slug",
    name: "Supplier",
    component: Supplier
  },
  {
    path: "/site/gas-produttori",
    name: "GasSuppliers",
    component: GasSuppliers
  },
  {
    path: "/site/gmaps",
    name: "Gmaps",
    component: Gmaps
  },
  {
    path: "/gas/:slugGas/:slugPage",
    name: "Gas",
    component: Gas
  },
  {
    path: "/promozioni",
    name: "Promotion",
    component: Promotion
  },
  {
    path: "/social-market",
    name: "SocialMarketOrders",
    component: SocialMarketOrders
  },
  /*
  {
    path: "/site/social-market/get-articles/:market_id",
    name: "SocialShop",
    component: SocialShop
  }*/
  /*
   * url non trovato
   * non aggiungere path dopo perche' verranno sovrascritti da /*
   */
  {
    path: "/*",
    name: "404",
    component: Home
  },
  {
    path: "/site/*",
    name: "site404",
    component: Home
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
