import Vue from "vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import App from "./App.vue";
import router from "./router";
import store from "./stores/store";
import axios from "axios";

/* Vue Validation 
const config = {
  fieldsBagName: "fields",
  errorBagName: "errors",
  classes: true,
  strict: false,
  classNames: {
    valid: "",
    invalid: "is-invalid"
  },
  events: "change|blur",
  validity: false,
  locale: "it",
  inject: true,
  aria: true,
  delay: 20
};*/
import { ValidationObserver, ValidationProvider, extend } from "vee-validate";
import * as rules from "vee-validate/dist/rules";
import { messages } from "vee-validate/dist/locale/it.json";

Object.keys(rules).forEach(rule => {
  extend(rule, { ...rules[rule], message: messages[rule] });
});

export default {
  name: "Example",
  components: {
    ValidationProvider,
    ValidationObserver
  },
  data: () => ({
    password: "password",
    password_confirm: "password_confirm"
  }),
  methods: {}
};

Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);

// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.config.productionTip = false;

// setup fake backend
import { configureFakeBackend } from "./helpers/fake-backend";
configureFakeBackend();

window.axios = axios;
/*
 * CSRF
 * in Application disabilitato per tutte i prefix api
 */
window.axios.defaults.headers.common["X-CSRF-Token"] = csrfToken;
window.axios.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';

window.axios.defaults.headers["Access-Control-Allow-Origin"] =
  "http://localhost:8080"; // http://localhost:8080
window.axios.defaults.headers["Access-Control-Allow-Methods"] = "*";
window.axios.defaults.headers["Access-Control-Allow-Credentials"] = true;
window.axios.defaults.headers["Access-Control-Allow-Headers"] = "*";

window.axios.defaults.headers.common["Access-Control-Allow-Origin"] =
  "http://localhost:8080"; // http://localhost:8080
window.axios.defaults.headers.common["Access-Control-Allow-Methods"] = "*";
window.axios.defaults.headers.common["Access-Control-Allow-Credentials"] = true;
window.axios.defaults.headers.common["Access-Control-Allow-Headers"] = "*";

// import "bootstrap";
// import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";

export const vm = new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");

/*
function common() {
  return true;
}
*/
