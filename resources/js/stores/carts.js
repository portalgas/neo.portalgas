export const carts = {
  state: {
    isLoading: true,
    cartArticles: [],
    currentArticle: {}, // nel dettaglio persisto l'articolo in store
    showPopupCart: false
  },
  getters: {
    getArticleInCart: state => state.cartArticles,
    getCurrentArticle: state => state.currentArticle,
    getPopupCart: state => state.showPopupCart,
    cartTotal: state => {
      let res = 0;
      state.cartArticles.map(item => {
        res += item.article.price * item.qty;
      });
      return res;
    },
    isProductLoading: state => {
      return state.isLoading;
    }
  },
  mutations: {
    LOAD_PAGE: state => {
      state.isLoading = false;
    },
    ADD_ARTICLE: (state, { article, qty }) => {

      var _this = this;

      console.log("ADD_ARTICLE article " + article.ids.article_id + " qty " + qty);

      const cartArticle = {
        article: Object.assign({}, article),
        qty: qty
      };
      
      console.info("ADD_ARTICLE article.ids.article_id "+article.ids.article_id);
      console.info("ADD_ARTICLE articoli in STORE "+state.cartArticles.length);
      
      var articleInCart = null;
      // if(typeof state.cartArticles.article !== 'undefined' && typeof state.cartArticles.article.ids !== 'undefined') {
      if(state.cartArticles.length>0) {
          articleInCart = state.cartArticles.find(
            element => (element.article.ids.article_id == article.ids.article_id && 
                       element.article.ids.article_organization_id == article.ids.article_organization_id && 
                       element.article.ids.order_id == article.ids.order_id && 
                       element.article.ids.organization_id == article.ids.organization_id)
            // element => (element.article.ids.equals(article.ids))             
          );
      }

      if (articleInCart !== null) {
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
    REMOVE_CART_ITEM: (state, { item }) => {
      const record = state.cartArticles.find(element => (element.article['ids'].equals(item.ids)));
      state.cartArticles.splice(state.cartArticles.indexOf(record), 1);
    },
    CURRENT_ARTICLE: (state, article) => {
      state.currentArticle = article;
    },
    SHOW_POPUP_CART: state => {
      state.showPopupCart = !state.showPopupCart;
    }
  },
  actions: {
    loadPage: context => {
      context.commit("LOAD_PAGE");
    },
    addArticle: (context, { article, qty }) => {
      console.log("store addArticle " + article.ids.article_id);
      context.commit("ADD_ARTICLE", { article, qty });
    },
    removeArticle: (context, index) => {
      context.commit("REMOVE_ARTICLE", index);
    },
    removeItemInCart: (context, { item }) => {
      context.commit("REMOVE_CART_ITEM", { item });
    },
    currentArticle: (context, article) => {
      context.commit("CURRENT_ARTICLE", article);
    },
    showOrHiddenPopupCart: context => {
      context.commit("SHOW_POPUP_CART");
    }
  },
  modules: {}
};
