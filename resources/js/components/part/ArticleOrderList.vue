<template>

  <div v-if="order!=null" class="row" :class="'card-'+order.type_draw">
        
        <div class="content-img-article col-sm-2 col-md-2 col-lg-2 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
          <img v-if="article.img1!=''" class="img-article responsive" :src="article.img1" :alt="article.name" />
          <div v-if="article.is_bio" class="box-bio">
              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
          </div>
        </div>

        <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xs-6">

                <div class="card-header bg-primary d-flex justify-content-between bd-highlight mb-3" :class="justInCart"> 
                    <div v-html="$options.filters.highlight(article.name)"></div>

                    <div v-if="order.order_type.code!='PROMOTION_GAS_USERS'" class="">
                      <a @click="clickShowOrHiddenModalArticleOrder()" class="cursor-pointer">
                        <i class="fas fa-search"></i></a>
                    </div>

                </div>

                <div v-if="article.descri!=null && article.descri!=''" v-html="$options.filters.highlight($options.filters.shortDescription(article.descri))">             
                </div>
          
                <div v-if="isLoading" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 

                <div>
                  <strong>Prezzo</strong> {{ article.price | currency }} &euro;
                    <del v-if="article.price_pre_discount != null"
                        >{{ article.price_pre_discount | currency }} &euro;</del
                      > 

                    <span v-if="article.price_pre_discount != null" class="price-promotion"></span>                 
                </div>
                <div>
                  <strong>Conf.</strong> {{ article.conf }}
                  <small class="text-muted"><strong>Prezzo/UM</strong> {{ article.um_rif_label }}</small>
                </div>

            <hr />
                <div v-if="article.package>1">
                  <small class="text-muted"><strong>Pezzi in confezione</strong> {{ article.package }}</small>
                </div>
                <div v-if="article.qta_multipli>1">
                  <small class="text-muted"><strong>Multipli</strong> {{ article.qta_multipli }}</small>
                </div>
                <div v-if="article.qta_minima>1">
                  <small class="text-muted"><strong>Q.tà minima</strong> {{ article.qta_minima }}</small>
                </div>

                <span v-if="order.order_type.code == 'PROMOTION'">
                    <!-- per la promozione, qta_massima = qta_minima_order = qta_massima_order: qta da raggiungere per la promozione -->
                    <div v-if="article.qta_massima_order>0">
                      <small class="text-muted"><strong>Promozione valida</strong> se sull'ordine totale si raggiungerà la quantità di <strong>{{ article.qta_massima_order }} acquisti</strong> (acquistati ora {{ article.qta_cart }})</small>
                    </div>                
                </span>
                <span v-if="order.order_type.code != 'PROMOTION' && order.order_type.code!='PROMOTION_GAS_USERS'">
                    <div v-if="article.qta_massima>0">
                      <small class="text-muted"><strong>Q.tà massima</strong> {{ article.qta_massima }}</small>
                    </div>                
                    <div v-if="article.qta_minima_order>0">
                      <small class="text-muted"><strong>Q.tà minima sull'ordine totale</strong> {{ article.qta_minima_order }}</small>
                    </div>                
                    <div v-if="article.qta_massima_order>0">
                      <small class="text-muted"><strong>Q.tà massima sull'ordine totale</strong> {{ article.qta_massima_order }} (acquistati ora {{ article.qta_cart }})</small>
                    </div>
                </span>

        </div>
        <div v-bind:class="'col-btn col-6 col-sm-6 col-md-4 col-lg-4 col-xs-4 '+justInCart"> 
           <app-btn-cart-add
                v-on:emitCartSave="emitCartSave"
                v-bind:article="article"
                v-bind:order="order"
                v-bind:is_public="is_public"></app-btn-cart-add>
        </div>
  </div>

</template>


<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ['order', 'article', 'is_public'],
  data() {
    return {
      isLoading: false,
      qta: 1
    };
  },
  components: {
    appBtnCartAdd: btnCartAdd
  },
  mounted() {
  },
  methods: {
    ...mapActions(['showModalArticleOrder', 'showOrHiddenModalArticleOrder', 'addModalContent', 'clearModalContent']),
    emitCartSave() {
      // console.log('emitCartSave', 'ArticleOrderList');
      this.$emit('emitCartSave', true);
    },
    clickShowOrHiddenModalArticleOrder () {
      var _this = this;
      
      _this.isLoading=true;
      _this.clearModalContent();

      let params = {
        order_id: this.article.ids.order_id,
        article_organization_id: this.article.ids.article_organization_id,
        article_id: this.article.ids.article_id
      };

      let url = "/admin/api/article-orders/get";
      axios
        .post(url, params)
        .then(response => {
            /*console.log(response.data);*/
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: response.data.results.articlesOrder.name,
                body: '',
                entity: response.data.results,
                footer: '',
                msg: ''
              }            

              _this.isLoading=false;

              _this.addModalContent(modalContent);
              _this.showOrHiddenModalArticleOrder();              
            }
        })
        .catch(error => {
          this.isLoading=false;
          this.isRunDeliveries=false;
          console.error("Error: " + error);
        });
    },  
  },
  computed: {
    justInCart: function() { 
        /* console.log("Article::justInCart article.cart.qta "+this.article.cart.qta) */
        return this.article.cart.qta>0 ? 'just-in-cart' : 'no-just-in-cart';
    } 
  },
  filters: {
    currency(amount) {
      let locale = window.navigator.userLanguage || window.navigator.language;
      locale = 'it-IT';
      const amt = Number(amount);
      return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
    },  
    shortDescription(value) {
      if (value && value.length > 75) {
        return value.substring(0, 75) + "...";
      } else {
        return value;
      }
    },
    highlight(text) {
      let q = $('#q-article').val();
      if(q!='')
        return text.replace(new RegExp(q, 'gi'), '<span class="highlighted" style="text-decoration: underline;color: #fa824f">$&</span>');
      else
        return text;
    },
  }
};
</script>

<style scoped>
.card {
  border: 1px solid #f8f9fa !important;
  margin-bottom: 10px;
  background-color: transparent;
}
.card:hover {
  background-color: #f8f8f8;
  box-shadow: 3px 3px 5px 0px;
}
.highlighted { 
  color: #fa824f;
  text-decoration: underline;
}
.card-header.just-in-cart {
  /* background-color:#fa824f !important; orange */
  background-color:#149334 !important;
}
.card-footer {
    padding: 0.75rem 0 !important;
}
.card-footer.just-in-cart {
  background-color:#f8f9fa !important;
}
.card-footer.no-just-in-cart {
  background-color: transparent !important;
}
.price-promotion {
  float: right;
  padding: 28px;
  margin-left: 15px;  
  background-image: url("/img/promotion-50w-55h.png");
  background-repeat: no-repeat, no-repeat;
  background-position: right center;
}
/*
 * override my.css
 */
.box-bio {
    top: 0px !important;
} 
</style>
