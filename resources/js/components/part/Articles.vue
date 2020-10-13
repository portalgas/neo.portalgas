<template>

  <div class="card">
        <div class="card-header bg-primary">{{ article.name }}</div>
        
        <img class="card-img-top responsive" :src="article.img1" :alt="article.name">
        <div v-if="article.is_bio" class="box-bio">
            <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica">
        </div>

        <div class="card-body">
            <p class="card-text">
                
                <div v-if="article.descri!=''">
                    {{ article.descri | shortDescription }} <a class="card-link" @click="clickshowOrHiddenModal()">..maggior dettaglio</a>
                </div>

                <del v-if="article.price_pre_discount != null"
                    >{{ article.price_pre_discount | currency }} &euro;</del
                  >
                  <strong>Prezzo</strong> {{ article.price | currency }} &euro;
            </p>
            <p class="card-text">
              <strong>Conf.</strong> {{ article.conf }}
              <small class="text-muted"><strong>Prezzo/UM</strong> {{ article.um_rif_label }}</small>
              <p v-if="article.qty_min>1">
                <small class="text-muted"><strong>Qta minima</strong> {{ article.qty_min }}</small>
              </p>
              <p v-if="article.qty_max>0">
                <small class="text-muted"><strong>Qta massma</strong> {{ article.qty_max }}</small>
              </p>

               <div v-if="article.descri==''">
                  <a class="card-link" @click="clickshowOrHiddenModal()">..maggior dettaglio</a>
                </div>              
            </p>
        </div>
        <div v-bind:class="'card-footer '+justInCart">
           <app-btn-cart-add v-bind:article="article"></app-btn-cart-add>
        </div>
  </div>

</template>

 

 
<script>
import { mapActions } from "vuex";
import btnCartAdd from "../../components/part/BtnCartAdd.vue";

export default {
  name: "app-article",
  props: ["article"],
  data() {
    return {
      qty: 1
    };
  },
  components: {
    appBtnCartAdd: btnCartAdd
  },
  mounted() {},
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    clickShowModal () {
      this.showModal(true);
    }, 
    clickshowOrHiddenModal () {

      var modalContent = {
        title: this.article.name,
        body: this.article.descri+"\r\n"+this.article.ingredients,
        footer: null
      }

      this.addModalContent(modalContent);
      this.showOrHiddenModal();
    },  
  },
  computed: {
    justInCart: function() { 
    console.log("article.cart.qty "+this.article.cart.qty)
        return this.article.cart.qty>0 ? 'bg-light' : 'bg-transparent';
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
    }
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
.card-img-top {
    display: block;
    height: 225;
    object-fit: cover;
    object-position: center;
    overflow: hidden;
    width: 225;
}
</style>
