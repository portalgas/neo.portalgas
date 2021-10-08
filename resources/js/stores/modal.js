export const modal = {
  state: {
    content: {
      title: null,
      body: null,
      footer: null,
      entity: null,
      msg: null
    },
    run: false,
    showModal: false,
    showModalArticleOrder: false,
    showModalSupplier: false,
    showModalSupplierImport: false,
  },
  getters: {
    getModalContent: state => {
      return state.content;
    },    
    getShowModal: state => state.showModal,
    getShowModalArticleOrder: state => state.showModalArticleOrder,
    getShowModalSupplier: state => state.showModalSupplier,
    getShowModalSupplierImport: state => state.showModalSupplierImport,
  },  
  mutations: {
    RUN_CONTENT: state => {
      state.run = true;
    },
    ADD_CONTENT: (state, { title, body, footer, entity, msg }) => {
      state.content = {
        title,
        body,
        footer,
        entity,
        msg
      };
    }, 
    CLEAR_CONTENT: state => {
      state.content = {
        title: null,
        body: null,
        footer: null,
        entity: null,
        msg: null
      };
    },
    SHOW_MODAL: (state) => {
      state.showModal = !state.showModal;
    },  
    SHOW_MODAL_SUPPLIER: (state) => {
      state.showModalSupplier = !state.showModalSupplier;
      if(!state.showModalSupplier) {
      
      }
    },
    SHOW_MODAL_ARTICLE_ORDER: (state) => {
      state.showModalArticleOrder = !state.showModalArticleOrder;
      if(!state.showModalArticleOrder) {
      
      }
    },  
    SHOW_MODAL_SUPPLIER_IMPORT: (state) => {
      state.showModalSupplierImport = !state.showModalSupplierImport;
      if(!state.showModalSupplierImport) {
      
      }
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
    clearModalContent: ({ commit }) => {
      commit("CLEAR_CONTENT");
    }, 
    showOrHiddenModal: (context) => {
      context.commit('SHOW_MODAL');
    },
    showOrHiddenModalArticleOrder: (context) => {
      context.commit('SHOW_MODAL_ARTICLE_ORDER');
    },
    showOrHiddenModalSupplier: (context) => {
      context.commit('SHOW_MODAL_SUPPLIER');
    },
    showOrHiddenModalSupplierImport: (context) => {
      context.commit('SHOW_MODAL_SUPPLIER_IMPORT');
    },
  },
  modules: {}
};