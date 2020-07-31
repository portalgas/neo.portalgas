import { vm } from "../app.js";
import axios from "axios";
import { authorizationHeader } from "./authorizationHeader";
/* import { jsonMixin } from "@/mixins/jsonMixin"; */

// const baseUrl = "http://neo.portalgas.local.it:81/api"; // global.window.AppConfig.apiBaseUrl;
const baseUrl = "/api"; // global.window.AppConfig.apiBaseUrl;

var header_defaults = {
  "Content-Type": "application/json",
  Accept: "application/json, text/javascript, */*; q=0.01",
  "X-Requested-With": "XMLHttpRequest"
};
var header_authorizations = authorizationHeader();

var headers = {};
/*
headers = jsonMixin.concat(headers, header_defaults);
headers = jsonMixin.concat(headers, header_authorizations);
*/
console.log(headers);

const http = axios.create({
  baseURL: baseUrl,
  timeout: 10000,
  headers: headers
});

http.interceptors.response.use(
  response => {
    let data = response.data;

    const authorizationHeader = "Authorization"; // global.window.AppConfig.authorizationHeader.toLowerCase();

    if (response.headers && response.headers[authorizationHeader]) {
      const token = response.headers[authorizationHeader].split(" ")[1];
      if (token) {
        data.auth = {
          token
        };
      }
    }

    return Promise.resolve(data);
  },
  error => {
    if (error.response.status === 401) {
      vm.$router.push({ name: "About" });
    } else if (error.response.status === 403) {
      // Forbidden Missing CSRF token cookie
      vm.$router.push({ name: "Account" });
    } else if (error.response.status === 422) {
      if (error.response.data.errors) {
        for (let key in error.response.data.errors) {
          vm.$validator.errors.add({
            field: key,
            msg: error.response.data.errors[key]
          });
        }
      }
    } else if (
      [400, 409].indexOf(error.response.status) !== -1 &&
      typeof error.response.data !== "undefined"
    ) {
      return Promise.reject(error.response.data);
    } else {
      console.error(error);
    }

    return Promise.reject(error.response);
  }
);

export default http;
