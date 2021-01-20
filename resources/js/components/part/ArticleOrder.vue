<template>

  <div v-if="order!=null" class="card" :class="'card-'+order.type_draw">
        <div class="card-header bg-primary" v-html="$options.filters.highlight(article.name)" :class="justInCart"></div>

        <div class="content-img-article" v-if="order.type_draw=='COMPLETE' || order.type_draw=='PROMOTION'">
          <img v-if="article.img1!=''" class="img-article responsive" :src="article.img1" :alt="article.name">
          <div v-if="article.is_bio" class="box-bio">
              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
          </div>
        </div>

        <div class="card-body">
            <div class="card-text">
                <div v-if="article.descri!=''" v-html="$options.filters.highlight($options.filters.shortDescription(article.descri))">             
                </div><span><a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModal()">maggior dettaglio</a></span>

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
                <div v-if="article.qta_massima>0">
                  <small class="text-muted"><strong>Q.tà massima</strong> {{ article.qta_massima }}</small>
                </div>

                <span v-if="order.order_type.code == 'PROMOTION'">
                    <!-- per la promozione, qta_minima_order = qta_massima_order: qta da raggiungere per la promozione -->
                    <div v-if="article.qta_massima_order>0">
                      <small class="text-muted"><strong>Promozione valida</strong> se sull'ordine totale si raggiungerà la quantità di <strong>{{ article.qta_massima_order }} acquisti</strong> (acquistati ora {{ article.qta_cart }})</small>
                    </div>                
                </span>
                <span v-if="order.order_type.code != 'PROMOTION'">
                    <div v-if="article.qta_minima_order>0">
                      <small class="text-muted"><strong>Q.tà minima sull'ordine totale</strong> {{ article.qta_minima_order }}</small>
                    </div>                
                    <div v-if="article.qta_massima_order>0">
                      <small class="text-muted"><strong>Q.tà massima sull'ordine totale</strong> {{ article.qta_massima_order }} (acquistati ora {{ article.qta_cart }})</small>
                    </div>
                </span>

            </div>
        </div>
        <div v-bind:class="'card-footer '+justInCart"> 
           <app-btn-cart-add v-bind:article="article" v-bind:order="order"></app-btn-cart-add>
        </div>
  </div>

</template>


<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ['order', 'article'],
  data() {
    return {
      qta: 1
    };
  },
  components: {
    appBtnCartAdd: btnCartAdd
  },
  mounted() {
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    clickShowModal () {
      this.showModal(true);
    }, 
    clickShowOrHiddenModal () {

      let params = {
        order_id: this.article.ids.order_id,
        article_organization_id: this.article.ids.article_organization_id,
        article_id: this.article.ids.article_id
      };

      let url = "/admin/api/html-article-orders/get";
      axios
        .post(url, params)
        .then(response => {
            // console.log(response.data);
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.article.name,
                body: response.data,
                footer: ''
              }            

              this.addModalContent(modalContent);
              this.showOrHiddenModal();              
            }
        })
        .catch(error => {
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
