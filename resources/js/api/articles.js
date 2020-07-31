import http from "./http";

export const articles = {
  get: function() {
    return http.post(`/supplier-organizations/gets`);
  }
};
