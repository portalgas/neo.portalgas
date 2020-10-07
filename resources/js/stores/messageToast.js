export const messageToast = {
  state: {
    message: {
      class: "primary",
      title: "",
      subtitle: "",
      body: "",
      append: false,
      target: "b-toaster-top-center",
      timeoutEvent: null
    }
  },
  getters: {
    getMessageToast: state => {
      return state.message;
    }
  },
  mutations: {
    ADD_MESSAGE_TOAST: (state, message) => {
      state.message = {
        class: message.class,
        title: message.title,
        subtitle: message.subtitle,
        body: message.body,
        append: message.append,
        target: message.target
      };

      if (state.timeoutEvent) {
        clearTimeout(state.timeoutEvent);
      }
      state.timeoutEvent = setTimeout(function() {
        state.message = {
          class: "",
          title: "",
          subtitle: "",
          body: "",
          append: false,
          target: ""
        };
      }, 5000);
    },
    CLEAR_MESSAGE_TOAST: state => {
      state.message = {
        class: "",
        title: "",
        subtitle: "",
        body: "",
        append: false,
        target: ""
      };
    },
  },
  actions: {
    addMessageToast: ({ commit }, obj) => {
      commit("ADD_MESSAGE_TOAST", obj);
    },
    clearMessageToast: commit => {
      commit("CLEAR_MESSAGE_TOAST");
    }
  },
  modules: {}
};