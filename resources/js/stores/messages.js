export const messages = {
  state: {
    isLoading: true,
    cartArticles: [],
    currentArticle: {}, // nel dettaglio persisto l'articolo in store
    showModal: false,
    showPopupCart: false,
    messageGroup: {
      // messageClass: 'danger',
      // message: 'Test'
      messageClass: "",
      message: "",
      timeoutEvent: null
    }
  },
  getters: {
    getArticleInCart: state => state.cartArticles,
    getCurrentArticle: state => state.currentArticle,
    getShowModal: state => state.showModal,
    getPopupCart: state => state.showPopupCart,
    cartTotal: state => {
      let res = 0;
      state.cartArticles.map(item => {
        res += item.article.prezzo * item.qty;
      });
      return res;
    },
    isProductLoading: state => {
      return state.isLoading;
    },
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
    ADD_ARTICLE: (state, { organization_id, article_organization_id, article_id, order_id, article, qty }) => {
      console.log("ADD_ARTICLE article.article_id " + article_id + " qty " + qty);

      const cartArticle = {
        organization_id: article.organization_id,
        article_organization_id: article.article_organization_id,
        article_id: article.article_id,
        order_id: article.order_id,
        article: Object.assign({}, article),
        qty: qty
      };

      const articleInCart = state.cartArticles.find(
        element => (element.organization_id == article.organization_id && 
                    element.article_organization_id == article.article_organization_id && 
                    element.article_id == article.article_id && 
                    element.order_id == article.order_id)
      );
      if (typeof articleInCart != "undefined") {
        console.log(
          "l'articolo è stato già acquistato con qty " + articleInCart.qty
        );
        // qty = qty - articleInCart.qty;
        articleInCart.qty = qty;
      } else {
        console.log("l'articolo NON mai stato acquistato => push");
        state.cartArticles.push(cartArticle);
      }

      console.log(state.cartArticles);
    },
    REMOVE_ARTICLE: (state, index) => {
      state.cartArticles.splice(index, 1);
    },
    UPDATE_CART: (state, { organization_id, article_organization_id, article_id, order_id, article, qty, isAdd }) => {
      const record = state.cartArticles.find(element => (element.organization_id == article.organization_id && 
                                                        element.article_organization_id == article.article_organization_id && 
                                                        element.article_id == article.article_id && 
                                                        element.order_id == article.order_id));
      if (record) {
        if (isAdd) {
          record.qty += qty;
        } else {
          record.qty = qty;
        }
      } else {
        const cart = {
          organization_id: article.organization_id,
          article_organization_id: article.article_organization_id,
          article_id: article.article_id,
          order_id: article.order_id,
          article: Object.assign({}, article),
          qty: 1
        };
        state.cartArticles.push(cart);
      }
    },
    REMOVE_CART_ITEM: (state, { item }) => {
      const record = state.cartArticles.find(element => (element.organization_id == item.organization_id && 
                                                        element.article_organization_id == item.article_organization_id && 
                                                        element.article_id == item.article_id && 
                                                        element.order_id == item.order_id));
      state.cartArticles.splice(state.cartArticles.indexOf(record), 1);
    },
    CURRENT_ARTICLE: (state, article) => {
      state.currentArticle = article;
    },
    SHOW_MODAL: state => {
      state.showModal = !state.showModal;
    },
    SHOW_POPUP_CART: state => {
      state.showPopupCart = !state.showPopupCart;
    }
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
    addArticle: (context, { organization_id, article_organization_id, article_id, order_id, article, qty }) => {
      console.log("store addArticle article_id " + article_id + " article.article_organization_id " + article.article_organization_id);
      context.commit("ADD_ARTICLE", { organization_id, article_organization_id, article_id, order_id, article, qty });
    },
    removeProduct: (context, index) => {
      context.commit("REMOVE_ARTICLE", index);
    },
    updateCart: (context, cart) => {
      // TODO: Call service
      context.commit("UPDATE_CART", cart);
      if (cart.isAdd) {
        let message_obj = {
          message: `Add ${cart.article.name} to cart successfully`,
          messageClass: "success",
          autoClose: true
        };
        context.commit("ADD_MESSAGE", message_obj);
      }
    },
    removeItemInCart: (context, { item }) => {
      context.commit("REMOVE_CART_ITEM", { item });
    },
    currentArticle: (context, article) => {
      context.commit("CURRENT_ARTICLE", article);
    },
    showOrHiddenModal: context => {
      context.commit("SHOW_MODAL");
    },
    showOrHiddenPopupCart: context => {
      context.commit("SHOW_POPUP_CART");
    }
  },
  modules: {}
};
