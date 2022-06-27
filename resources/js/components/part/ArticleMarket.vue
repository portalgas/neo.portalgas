<template>

  <div v-if="market!=null" class="card">
        <div class="card-header bg-primary" v-html="$options.filters.highlight(article.name)" :class="justInCart"></div>

        <div class="content-img-article">
          <img v-if="article.img1!=''" class="img-article responsive" :src="article.img1" :alt="article.name">
          <div v-if="article.is_bio" class="box-bio">
              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
          </div>
        </div>

        <div class="card-body">
            <div class="card-text">
                <div v-if="article.descri!=null && article.descri!=''" v-html="$options.filters.highlight($options.filters.shortDescription(article.descri))">             
                </div>

                <span>
                  <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModal()">maggior dettaglio</a>
                  
                  <div v-if="isLoading" class="box-spinner"> 
                    <div class="spinner-border text-info" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>  
                  </div> 

                </span>

                <div>
                  <strong>Prezzo</strong> {{ article.prezzo | currency }} &euro;
                    <del v-if="article.price_pre_discount != null"
                        >{{ article.price_pre_discount | currency }} &euro;</del
                      > 

                    <span v-if="article.price_pre_discount != null" class="price-promotion"></span>                 
                </div>
                <div>
                  <strong>Conf.</strong> {{ article.conf }}
                  <small class="text-muted"><strong>Prezzo/UM</strong> {{ article.um_rif_label }}</small>
                </div>
            </div>

            <div class="card-text">
                <div v-if="article.package>1">
                  <small class="text-muted"><strong>Pezzi in confezione</strong> {{ article.package }}</small>
                </div>
                <div v-if="article.qta_multipli>1">
                  <small class="text-muted"><strong>Multipli</strong> {{ article.qta_multipli }}</small>
                </div>
                <div v-if="article.qta_minima>1">
                  <small class="text-muted"><strong>Q.tà minima</strong> {{ article.qta_minima }}</small>
                </div>

            </div>
        </div>
        <div v-bind:class="'card-footer '+justInCart"> 
           <app-btn-cart-add v-bind:article="article"
                             v-bind:order="order"
                             v-bind:is_social_market="is_social_market"></app-btn-cart-add>
        </div>
  </div>

</template>


<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ['market', 'article'],
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
    clickShowOrHiddenModal () {

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
            // console.log(response.data);
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.article.name,
                body: '',
                entity: response.data.results,
                footer: '',
                msg: ''
              }               

              _this.isLoading=false;

              _this.addModalContent(modalContent);
              _this.showOrHiddenModal();              
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
@media screen and (min-width: 600px) {
  .card-SIMPLE {
    min-height: 285px;
  }
  .card-COMPLETE, .card-PROMOTION {
    min-height: 400px;
  }
}
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
</style>
