export const modal = {
  state: {
    content: {
      title: null,
      body: null,
      footer: null,
      entity: null
    },
    run: false,
    showModal: false,
    showModalSupplier: false,
    showModalSupplierImport: false,
  },
  getters: {
    getModalContent: state => {
      return state.content;
    },    
    getShowModal: state => state.showModal,
    getShowModalSupplier: state => state.showModalSupplier,
    getShowModalSupplierImport: state => state.showModalSupplierImport,
  },  
  mutations: {
    RUN_CONTENT: state => {
      state.run = true;
    },
    ADD_CONTENT: (state, { title, body, footer, entity }) => {
      state.content = {
        title,
        body,
        footer,
        entity
      };
    }, 
    CLEAR_CONTENT: state => {
      state.content = {
        title,
        body,
        footer,
        entity
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
    clearModalContent: commit => {
      commit("CLEAR_CONTENT");
    }, 
    showOrHiddenModal: (context) => {
      context.commit('SHOW_MODAL');
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