export const messages = {
  state: {
    messageGroup: {
      messageClass: "",
      message: "",
      timeoutEvent: null
    }
  },
  getters: {
    getMessage: state => {
      return state.messageGroup;
    }
  },
  mutations: {
    ADD_MESSAGE: (state, { message, messageClass }) => {
      state.messageGroup = {
        messageClass,
        message
      };

      if (state.timeoutEvent) {
        clearTimeout(state.timeoutEvent);
      }
      state.timeoutEvent = setTimeout(function() {
        state.messageGroup = {
          messageClass: "",
          message: ""
        };
      }, 5000);
    },
    CLEAR_MESSAGE: state => {
      state.messageGroup = {
        messageClass: "",
        message: ""
      };
    }
  },
  actions: {
    addMessage: ({ commit }, obj) => {
      console.log('addMessage addMessage addMessage ');
      commit("ADD_MESSAGE", obj);
    },
    clearMessage: commit => {
      commit("CLEAR_MESSAGE");
    }
  },
  modules: {}
};
