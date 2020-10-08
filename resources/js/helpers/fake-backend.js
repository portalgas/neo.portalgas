import axios from "axios";

export function configureFakeBackend() {
  let users = [
    {
      id: 1,
      username: "root",
      password: "root",
      firstName: "Test",
      lastName: "User"
    }
  ];
  let realFetch = window.fetch;
  let token = "";
  window.fetch = function(url, opts) {
    return new Promise((resolve, reject) => {
      console.log(url);

      // wrap in timeout to simulate server api call
      setTimeout(() => {
        // authenticate
        if (url.endsWith("/users/authenticate") && opts.method === "POST") {
          // get parameters from post request
          let body = JSON.parse(opts.body);
          /*
           * per non inviarli in formato json
           */
          const params = new URLSearchParams();
          params.append("username", body.username);
          params.append("password", body.password);
          console.log(params);
          axios
            .post("http://ecomm.local.it:81/api/tokenJwt/login", params)
            .then(response => {
              if (response.data.esito) {
                token = response.data.token;
              }
              console.log("token " + token);
            })
            .catch(error => {
              console.error("Error: " + error);
            });

          // find if any user matches login credentials
          let filteredUsers = users.filter(user => {
            return (
              user.username === body.username && user.password === body.password
            );
          });
          console.log("filteredUsers.length " + filteredUsers.length);
          if (filteredUsers.length) {
            // if login details are valid return user details and jwt token
            let user = filteredUsers[0];
            let responseJson = {
              id: user.id,
              username: user.username,
              firstName: user.firstName,
              lastName: user.lastName,
              token: token
            };
            resolve({
              ok: true,
              text: () => Promise.resolve(JSON.stringify(responseJson))
            });
          } else {
            // else return error
            reject("Username or password is incorrect");
          }

          return;
        }

        // get users
        if (url.endsWith("/users") && opts.method === "GET") {
          // check for fake auth token in header and return users if valid, this security is implemented server side in a real application
          if (opts.headers && opts.headers.Authorization === "Bearer") {
            resolve({
              ok: true,
              text: () => Promise.resolve(JSON.stringify(users))
            });
          } else {
            // return 401 not authorised if token is null or invalid
            reject("Unauthorised");
          }

          return;
        }

        // pass through any requests not handled above
        realFetch(url, opts).then(response => resolve(response));
      }, 500);
    });
  };
}
