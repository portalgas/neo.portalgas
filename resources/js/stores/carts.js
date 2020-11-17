export const carts = {
  state: {
    isLoading: true,
    cartArticles: [],
    currentArticle: {}, // nel dettaglio persisto l'articolo in store
    showPopupCart: false
  },
  getters: {
    getArticlesInCart: state => state.cartArticles,
    getCurrentArticle: state => state.currentArticle,
    getPopupCart: state => state.showPopupCart,
    cartTotal: state => {
      let res = 0;
      state.cartArticles.map(item => {
        res += item.article.price * item.qta;
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
    ADD_ARTICLE: (state, { article, qta }) => {

      var _this = this;

      console.log("ADD_ARTICLE article " + article.ids.article_id + " qta " + qta);

      const cartArticle = {
        article: Object.assign({}, article),
        qta: qta
      };
      
      console.info("ADD_ARTICLE article.ids.article_id "+article.ids.article_id);
      console.info("ADD_ARTICLE articoli in STORE "+state.cartArticles.length);
      
      var articleInCart = undefined;
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

      if (typeof articleInCart !== "undefined") {
        console.log("l'articolo FOUND ");
        console.log(articleInCart);
        console.log(
          "l'articolo è stato già acquistato con qta " + articleInCart.qta
        );
        // qta = qta - articleInCart.qta;
        articleInCart.qta = qta;
      } else {
        console.log("l'articolo NON mai stato acquistato => push");        
        state.cartArticles.push(cartArticle);
      }

      console.log(state.cartArticles);

      localStorage.setItem('cartArticles', JSON.stringify(state.cartArticles))
    },
    REMOVE_ARTICLE: (state, index) => {
      state.cartArticles.splice(index, 1);
    },
    REMOVE_CART_ITEM: (state, cart) => {
      console.log("REMOVE_CART_ITEM");
      console.log(cart);
      var article = cart.article;
      console.log(article);
      const record = state.cartArticles.find(element => (element.article.ids.article_id == article.ids.article_id && 
                       element.article.ids.article_organization_id == article.ids.article_organization_id && 
                       element.article.ids.order_id == article.ids.order_id && 
                       element.article.ids.organization_id == article.ids.organization_id)
                      // (element.article['ids'].equals(article.ids))
                      );
      state.cartArticles.splice(state.cartArticles.indexOf(record), 1);
    },
    UPDATE_CART: (state, { cart, qtaNew, isAdd }) => {
     
 console.log(cart);      
 console.log(cart.article); 
 console.log(cart.article.ids);  
  var article = cart.article;      
      const record = state.cartArticles.find(element => (element.article.ids.article_id == article.ids.article_id && 
                       element.article.ids.article_organization_id == article.ids.article_organization_id && 
                       element.article.ids.order_id == article.ids.order_id && 
                       element.article.ids.organization_id == article.ids.organization_id));
      console.log("UPDATE_CART record.qta "+record.qta+" qtaNew "+qtaNew);
      if (record) {
        if (isAdd) {
          record.qta += qtaNew;
        } else {
          record.qta = qtaNew;
        }
      } else {
        const cartNew = {
          cart: Object.assign({}, cart.article),
          qta: 1
        };
        state.cartArticles.push(cartNew);
      }
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
    addArticle: (context, { article, qta }) => {
      console.log("ADD_ARTICLE");
      console.log(article);
      console.log("qta "+qta);
      console.log("store addArticle " + article.ids.article_id);
      context.commit("ADD_ARTICLE", { article, qta });
    },
    removeArticle: (context, index) => {
      context.commit("REMOVE_ARTICLE", index);
    },
    removeItemInCart: (context, { article }) => {
      context.commit("REMOVE_CART_ITEM", { article });
    },
    updateArticle: (context, { cart, qtaNew, isAdd }) => {

       console.log(cart); 

      context.commit("UPDATE_CART", { cart, qtaNew, isAdd });
      if (isAdd) {
        let message_obj = {
          message: `Add qta ${qtaNew} ${cart.article.name} to cart successfully`,
          messageClass: "success",
          autoClose: true
        };
        context.commit("ADD_MESSAGE", message_obj);
      }
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
