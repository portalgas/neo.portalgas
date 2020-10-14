export const orders = {
  state: {
    order: {}
  },
  getters: {
    getOrder: state => {
      return state.order;
    }
  },
  mutations: {
    ADD_ORDER: (state, { order }) => {
      state.order = order;
    },
    CLEAR_ORDER: state => {
      state.order = {};
    }
  },
  actions: {
    addOrder: ({ commit }, order) => {
      console.log('addOrder ');
      console.log(order);
      commit("CLEAR_ORDER");
      commit("ADD_ORDER", order);
    },
    clearOrder: commit => {
      commit("CLEAR_ORDER");
    }
  },
  modules: {}
};
