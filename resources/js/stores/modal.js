export const modal = {
  state: {
    content: {
      title: null,
      body: null,
      footer: null
    },
    showModal: false,
  },
  getters: {
    getModalContent: state => {
      return state.content;
    },    
    getShowModal: state => state.showModal
  },  
  mutations: {
    ADD_CONTENT: (state, { title, body, footer }) => {
      state.content = {
        title,
        body,
        footer
      };
    },  
    CLEAR_CONTENT: state => {
      state.content = {
        title,
        body,
        footer
      };
    },
    SHOW_MODAL: (state) => {
      state.showModal = !state.showModal;
    },    
  },
  actions: {
    addModalContent: ({ commit }, obj) => {
      console.log('ADD_CONTENT ');
      commit("ADD_CONTENT", obj);
    },
    clearModalContent: commit => {
      commit("CLEAR_CONTENT");
    }, 
    showOrHiddenModal: (context) => {
      context.commit('SHOW_MODAL');
    },
  },
  modules: {}
};