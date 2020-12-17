<template>

  <div class="card" :class="'card-'+order.type_draw">
        <div class="card-header bg-primary" v-html="$options.filters.highlight(article.name)"></div>

        <div class="content-img-article" v-if="order.type_draw=='COMPLETE'">
          <img v-if="article.img1!=''" class="img-article responsive" :src="article.img1" :alt="article.name">
          <div v-if="article.is_bio" class="box-bio">
              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
          </div>
        </div>

        <div class="card-body">
            <div class="card-text">
                <div v-if="article.descri!=''" v-html="$options.filters.highlight($options.filters.shortDescription(article.descri))">             
                </div><span><a class="btn btn-primary btn-block" @click="clickShowOrHiddenModal()">maggior dettaglio</a></span>

                <div>
                  <strong>Prezzo</strong> {{ article.price | currency }} &euro;
                    <del v-if="article.price_pre_discount != null"
                        >{{ article.price_pre_discount | currency }} &euro;</del
                      >                
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
                <div v-if="article.qta_minima_order>0">
                  <small class="text-muted"><strong>Q.tà minima sull'ordine totale</strong> {{ article.qta_minima_order }}</small>
                </div>
                <div v-if="article.qta_massima>0">
                  <small class="text-muted"><strong>Q.tà massima</strong> {{ article.qta_massima }}</small>
                </div>
                <div v-if="article.qta_massima_order>0">
                  <small class="text-muted"><strong>Q.tà massima sull'ordine totale</strong> {{ article.qta_massima_order }} (acquistati ora {{ article.qta_cart }})</small>
                </div>
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
            console.log(response.data);
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
        return this.article.cart.qta>0 ? 'bg-light' : 'bg-transparent';
    } 
  },
  filters: {
    currency(amount) {
      let locale = window.navigator.userLanguage || window.navigator.language;
      const amt = Number(amount);
      return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
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
.card-SIMPLE {
  min-height: 285px;
}
.card-COMPLETE {
  min-height: 400px;
}
.card:hover {
  background-color: #f8f8f8;
  box-shadow: 3px 3px 5px 0px;
}
.box-bio {
    right: 0;
    padding: 10px;
    position: absolute;
    top: 30px;
    z-index: 1;    
}
.box-bio img {
    border-radius: 30px;
    float: left;
    height: 40px;
    margin-right: 5px;
    width: 40px;    
}
.highlighted { 
  color: #fa824f;
  text-decoration: underline;
}
</style>
