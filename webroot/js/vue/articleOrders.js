"use strict";

var articleOrders = null;

$(function () {
    
    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    articleOrders = new Vue({
      router,
      el: '#vue-article-orders',
      data: {
        select_articles_all: false,
        select_article_orders_all: false,
        can_edit: false,
        order: [],
        article_orders: [],
        articles: [],
        is_run: false,
        is_save: false
      },  
      methods: {
        selectArticleOrdersAll: function(e) {
            let _this = this;
            if(_this.article_orders.length>0) {
              _this.select_article_orders_all = !_this.select_article_orders_all;
              
              _this.article_orders.forEach(function (article_order, i) {
                  article_order.is_select = _this.select_article_orders_all;
              });   
            }
        },
        selectArticlesAll: function(e) {
          let _this = this;
          if(_this.articles.length>0) {
            _this.select_articles_all = !_this.select_articles_all;
            
            _this.articles.forEach(function (article, i) {
                article.is_select = _this.select_articles_all;
            });   
          }          
        },
        gets: function(e) {

            this.is_run = true;

            let organization_id = $("input[name='organization_id']").val(); 
            let order_id = $("input[name='order_id']").val();
            let order_type_id = $("input[name='order_type_id']").val();
            // console.log(ajaxUrlGetOrdersByDelivery+' delivery_id '+delivery_id);

            if(order_id==0 || order_id=='') {
                this.is_run = false;
                return;
            }

            let params = {
                organization_id: organization_id,
                order_type_id: order_type_id,
                order_id: order_id
            }; 

            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

            axios.post('/admin/api/article-orders/getAssociateToOrder', params)
                .then(response => {
                  // console.log(response.data); 
                  
                  this.is_run = false;
                  this.can_edit = response.data.results.can_edit;  
                  this.order = response.data.results.order;        
                  this.article_orders = response.data.results.article_orders;        
                  this.articles = response.data.results.articles;                  
                })
            .catch(error => {
                  this.is_run = false;
                  console.error("Error: " + error);
            });            
        },
        save: function(e) {
          e.preventDefault();
          
          let _this = this;
          
          _this.is_save = true;
          let _update_article_orders = [];
          let _delete_article_orders = [];
          let _articles = [];

          if(_this.article_orders.length>0) {
            _this.article_orders.forEach(function (article_order, i) {
              if(article_order.is_select) 
                _delete_article_orders.push(article_order);
              else 
                _update_article_orders.push(article_order);
            });   
          }  

          if(_this.articles.length>0) {
            /* 
             * prendo solo quelli scelti 
             */
            _this.articles.forEach(function (article, i) {
              if(article.is_select) 
                 _articles.push(article);
            });   
          }   
          
          let organization_id = $("input[name='organization_id']").val(); 
          let order_id = $("input[name='order_id']").val();
          let order_type_id = $("input[name='order_type_id']").val();
          
          let params = {
              organization_id: organization_id,
              order_type_id: order_type_id,
              order_id: order_id,
              articles: _articles,
              update_article_orders: _update_article_orders,
              delete_article_orders: _delete_article_orders,
          }; 
          console.log(params, 'params');

          axios.post('/admin/api/article-orders/setAssociateToOrder', params)
            .then(response => {
              console.log(response.data); 
              _this.is_save = false;   
              
              location.reload();
            })
            .catch(error => {
              _this.is_save = false;
              console.error("Error: " + error);
            }); 
                      
          return false;
        }      
      },
      mounted: function(){
        console.log('mounted articleOrders');
        this.gets();
      },
      filters: {
        currency(amount) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          const amt = Number(amount);
          return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
        },
        /*
         * formatta l'importo float che arriva dal database
         * da 1000.5678 in 1.000,57 
         * da 1000 in 1.000,00          
         */
        formatImportToDb: function(number) {
              var decimals = 2;
              var dec_point = ','; 
              var thousands_sep = '.';

              // console.log('formatImportToDb BEFORE number '+number);

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
              // console.log('formatImportToDb AFTER number '+number);

              return number;
          },         
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        },
          counter: function (index) {
            return index+1
        }
      }      
    });
});