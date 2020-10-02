import Vue from "vue";
import Vuex from "vuex";

import { messages } from "./messages";
import { messageToast } from "./messageToast";
import { messageAlert } from "./messageAlert";
import { authentication } from "./authentication";
import { users } from "./users";
import { carts } from "./carts";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    messages,
    messageToast,
    messageAlert,
    authentication,
    users,
    carts
  }
});
