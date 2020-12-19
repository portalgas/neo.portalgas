<template>

  <div class="row">
        <div class="col-sm-2 col-xs-2 col-md-2 d-none d-sm-block">
          <div class="content-img-article-small">
            <img v-if="article.img1!=''" class="img-article-small responsive" :src="article.img1" :alt="article.name">
            <div v-if="article.is_bio" class="box-bio">
                <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
            </div>
          </div>
        </div>
        <div class="col-text col-sm-3 col-xs-3 col-md-4">
          {{ article.name }} <span><a class="fas fa-search cursor-pointer" @click="clickShowOrHiddenModal()"></a></span>
        </div>
        <div class="col-text col-sm-1 col-xs-1 col-md-1">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo 
             </span>         
              {{ article.price | currency }} &euro;
                <del v-if="article.price_pre_discount != null"
                    >{{ article.price_pre_discount | currency }} &euro;</del
                  > 
        </div>
        <div class="col-text col-sm-1 col-xs-1 col-md-1">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Conf. 
             </span> 
              {{ article.conf }}

        </div>
        <div class="col-text col-sm-2 col-xs-2 col-md-1">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo/UM 
             </span>           
              {{ article.um_rif_label }}
        </div>
        <div class="col-text col-xs-3 col-md-3">
           <app-btn-cart-add v-bind:article="article" v-bind:order="order" :key="article.id"></app-btn-cart-add>
        </div>
  </div>

</template>

<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "user-cart-article",
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
.row {
  margin-bottom: 5px;
}
.col-text {
  padding-top: 10px
}
.box-bio {
    left: 0;
    top: 1px;
    position: absolute;
    z-index: 1;
}
.box-bio img {
    border-radius: 30px;
    float: left;
    height: 20px;
    margin-left: 5px;
    width: 20px;    
}
.highlighted { 
  color: #fa824f;
  text-decoration: underline;
}
</style>
