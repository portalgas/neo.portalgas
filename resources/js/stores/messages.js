export const messages = {
  state: {
    isLoading: true,
    showModal: false,
    messageGroup: {
      // messageClass: 'danger',
      // message: 'Test'
      messageClass: "",
      message: "",
      timeoutEvent: null
    }
  },
  getters: {
    getShowModal: state => state.showModal,
    messages: state => {
      return state.messageGroup;
    }
  },
  mutations: {
    LOAD_PAGE: state => {
      state.isLoading = false;
    },
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
    },
    SHOW_MODAL: state => {
      state.showModal = !state.showModal;
    },
  },
  actions: {
    loadPage: context => {
      context.commit("LOAD_PAGE");
    },
    addMessage: ({ commit }, obj) => {
      commit("ADD_MESSAGE", obj);
    },
    clearMessage: commit => {
      commit("CLEAR_MESSAGE");
    },    
    showOrHiddenModal: context => {
      context.commit("SHOW_MODAL");
    }
  },
  modules: {}
};
