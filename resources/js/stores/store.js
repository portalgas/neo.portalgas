import Vue from "vue";
import Vuex from "vuex";

import { authentication } from "./authentication";
import { users } from "./users";
import { messages } from "./messages";
import { modal } from "./modal";
import { orders } from "./orders";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    authentication,
    users,
    messages,
    modal,
    orders
  }
});
