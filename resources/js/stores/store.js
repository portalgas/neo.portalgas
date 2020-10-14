import Vue from "vue";
import Vuex from "vuex";

import { authentication } from "./authentication";
import { users } from "./users";
import { modal } from "./modal";
import { orders } from "./orders";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    authentication,
    users,
    modal,
    orders
  }
});
