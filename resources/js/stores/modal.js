export const modal = {
  state: {
    content: {
      title: null,
      body: null,
      footer: null
    },
    run: false,
    showModal: false,
  },
  getters: {
    getModalContent: state => {
      return state.content;
    },    
    getShowModal: state => state.showModal
  },  
  mutations: {
    RUN_CONTENT: state => {
      state.run = true;
    },
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
    runModal: commit => {
      /* console.log('RUN_CONTENT '); */
      commit("RUN_CONTENT");
    },
    addModalContent: ({ commit }, obj) => {
      /* console.log('ADD_CONTENT '); */
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