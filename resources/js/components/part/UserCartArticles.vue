<template>

  <div class="row">
        <div class="col-sm-2 col-xs-2 col-md-2 bg-primary" v-html="$options.filters.highlight(article.name)"></div>

        <div class="content-img-top">
          <img v-if="article.img1!=''" class="card-img-top responsive" :src="article.img1" :alt="article.name">
          <div v-if="article.is_bio" class="box-bio">
              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica">
          </div>
        </div>

        <div class="col-sm-8 col-xs-8 col-md-8">
            <p class="card-text">
                
              <div v-if="article.descri!=''" v-html="$options.filters.highlight($options.filters.shortDescription(article.descri))">             
              </div><span><a class="badge badge-primary" @click="clickShowOrHiddenModal()">..maggior dettaglio</a></span>

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
            </p>
            <p class="card-text">
              <div v-if="article.package>1">
                <small class="text-muted"><strong>Pezzi in confezione</strong> {{ article.package }}</small>
              </div>
              <div v-if="article.qty_multiple>1">
                <small class="text-muted"><strong>Multipli</strong> {{ article.qty_multiple }}</small>
              </div>
              <div v-if="article.qty_min>1">
                <small class="text-muted"><strong>Qta minima</strong> {{ article.qty_min }}</small>
              </div>
              <div v-if="article.qty_max>0">
                <small class="text-muted"><strong>Qta massma</strong> {{ article.qty_max }}</small>
              </div>

               <div v-if="article.descri==''">
                  <a class="card-link" @click="clickShowOrHiddenModal()">..maggior dettaglio</a>
                </div>              
            </p>
        </div>
        <div class="col-sm-2 col-xs-2 col-md-2">
           <app-btn-cart-add v-bind:article="article"></app-btn-cart-add>
        </div>
  </div>

</template>

 

 
<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ['article'],
  data() {
    return {
      qty: 1
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
                footer: this.article.descri+"\r\n"+this.article.ingredients
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
.box-bio {
    right: 0;
    padding: 10px;
    position: absolute;
    top: 30px;
}
.box-bio img {
    border-radius: 30px;
    float: left;
    height: 40px;
    margin-right: 5px;
    width: 40px;    
}
.content-img-top {
    text-align: center;
}
.card-img-top {
    /* display: block; */
    display: initial;    
    height: 225 px;
    object-fit: cover;
    object-position: center;
    overflow: hidden;
    width: 225 px;
}
.highlighted { 
  color: #fa824f;
  text-decoration: underline;
}
</style>
