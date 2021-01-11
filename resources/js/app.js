import Vue from "vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import App from "./App.vue";
import router from "./router";
import store from "./stores/store";
import axios from "axios";

// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

Vue.config.productionTip = false;

window.axios = axios;
/*
 * CSRF
 * in Application disabilitato per tutte i prefix api
 */
window.axios.defaults.headers.common["X-CSRF-Token"] = csrfToken;
window.axios.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
/*
window.axios.defaults.headers["Access-Control-Allow-Origin"] = "*";
window.axios.defaults.headers["Access-Control-Allow-Methods"] = "*";
window.axios.defaults.headers["Access-Control-Allow-Credentials"] = true;
window.axios.defaults.headers["Access-Control-Allow-Headers"] = "*";

window.axios.defaults.headers.common["Access-Control-Allow-Origin"] = "*"; 
window.axios.defaults.headers.common["Access-Control-Allow-Methods"] = "*";
window.axios.defaults.headers.common["Access-Control-Allow-Credentials"] = true;
window.axios.defaults.headers.common["Access-Control-Allow-Headers"] = "*";
*/

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
 * variabile che arriva da cake, dichiarata come variabile e in app.js settata a window.
 */
window.j_seo = j_seo;
window.organizationTemplatePayToDelivery = organizationTemplatePayToDelivery;

/*
 * per array.equals tra ids article e ids article persisititi in store
 * https://jsfiddle.net/SamyBencherif/8352y6yw/
 */
if(Array.prototype.equals)
    console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time 
    if (this.length != array.length)
        return false;

    for (var i = 0, l=this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;       
        }           
        else if (this[i] != array[i]) { 
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;   
        }           
    }       
    return true;
}