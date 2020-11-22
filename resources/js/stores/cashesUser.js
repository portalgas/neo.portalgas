export const cashesUser = {
  state: {
    reload: false
  },
  getters: {
    cashesUserReload: state => state.reload
  },
  mutations: {
    RELOAD: state => {
      state.reload = true;
    },
    RELOAD_FINISH: state => {
      state.reload = false;
    },
  },
  actions: {
    cashesUserReload: context => {
      context.commit("RELOAD");
    },
    cashesUserReloadFinish: context => {
      context.commit("RELOAD_FINISH");
    }
  },
  modules: {}
};
