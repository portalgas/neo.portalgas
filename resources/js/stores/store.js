import Vue from "vue";
import Vuex from "vuex";

import { messages } from "./messages";
import { authentication } from "./authentication";
import { users } from "./users";
import { carts } from "./carts";
import { modal } from "./modal";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    messages,
    authentication,
    users,
    carts,
    modal
  }
});
