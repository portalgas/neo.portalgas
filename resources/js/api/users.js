import http from "./http";

const APIURL = "";

export const users = {
  getUsers() {
    return http.get(`${APIURL}/users/index`);
  },
  getUser(id) {
    return http.get(`${APIURL}/users/view/${id}`);
  },
  addUser(data) {
    return http.post(`${APIURL}/users/add`, data);
  },
  editUser(data) {
    return http.put(`${APIURL}/users/edit/${data.id}`, data);
  },
  deleteUser(id) {
    return http.delete(`${APIURL}/users/delete/${id}`);
  },
  checkUniqueField(data) {
    return http.post(`${APIURL}/users/checkUniqueField`, data);
  },
  login(data) {
    console.log(data);

    const config = {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
        'X-Requested-With': 'XMLHttpRequest'
      }
    };

    return http.post(`${APIURL}/tokenJwt/login`, data, config);
    // return http.post(`${APIURL}/users/login`, data, config);
  }
};
