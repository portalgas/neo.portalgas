<template>
  <div>

    <transition name="fade">
      <div class="modal-wrapper" v-show="showModalArticleOrdersCart" tabindex="-1" role="dialog">

          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{modalContent.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi" @click="closeModal()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

                <section id="about" class="about" v-if="modalContent.order!=null && modalContent.order.articles_orders!=null">

                  <div class="content container aos-init aos-animate" data-aos="fade-up" :class="modalContent.order.order_type.name">

                    <div class="box-supplier-organization">
                        <span class="box-img">
                          <img :src="modalContent.order.suppliers_organization.supplier.img1" :alt="modalContent.order.suppliers_organization.supplier.name" :title="modalContent.order.suppliers_organization.supplier.name" width="50px" class="img-supplier" />
                        </span> 

                        <span class="box-name" v-if="modalContent.order.suppliers_organization.supplier.www!=''">
                            <a :href="modalContent.order.suppliers_organization.supplier.www" target="_blank" title="vai al sito del produttore">
                              {{ modalContent.order.suppliers_organization.supplier.name }}
                            </a>
                        </span> 

                        <span class="box-name" v-if="modalContent.order.suppliers_organization.supplier.www==''">
                            {{ modalContent.order.suppliers_organization.supplier.name }}
                        </span> 
                    </div>


                    <strong>Consegna</strong>
                  <span v-if="modalContent.order.delivery.sys=='Y'">
                      {{ modalContent.order.delivery.luogo }}
                  </span>
                  <span v-if="modalContent.order.delivery.sys!='Y'">
                      {{ modalContent.order.delivery.luogo }} il {{ modalContent.order.delivery.data | formatDate }}
                  </span>
       

                    <span v-if="modalContent.order.order_type_id!=9 && modalContent.order.hasTrasport=='N'" class="badge badge-secondary">Non ha spese di trasporto</span>
                    <span v-if="modalContent.order.order_type_id!=9 && modalContent.order.hasTrasport=='Y'" class="badge badge-warning">Ha spese di trasporto</span>

                    <span v-if="modalContent.order.order_type_id!=9 && modalContent.order.hasCostMore=='N'" class="badge badge-secondary">Non ha costi aggiuntivi</span>
                    <span v-if="modalContent.order.order_type_id!=9 && modalContent.order.hasCostMore=='Y'" class="badge badge-warning">Ha costi aggiuntivi</span>
                          

                    <span class="badge badge-pill" :class="'text-color-background-'+modalContent.order.order_state_code.css_color" :style="'background-color:'+modalContent.order.order_state_code.css_color">{{ modalContent.order.order_state_code.name }}</span>
                    <span v-if="modalContent.order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ modalContent.order.order_type.descri }}</span>

                        <span v-for="(articles_order, index) in modalContent.order.articles_orders">
                        <ul v-if="articles_order.carts.length>0" v-for="(cart, index) in articles_order.carts"> 
                          <li>

                            <div class="row" v-if="articles_order.is_bio!='' || articles_order.img1!=''">
                              <div class="col-4 col-label">
                                <span class="box-bio" v-if="articles_order.is_bio!=''">
                              <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
                              </span>
                              </div>
                              <div class="col-8">
                                <span class="box-img" v-if="articles_order.img1!=''">
                                    <img :src="articles_order.img1" class="img-article" />
                                </span>
                              </div>
                            </div>

                            <b>{{ articles_order.name }}</b> - {{ cart.final_qta }} di {{ articles_order.conf }} * {{ articles_order.price  | currency  }}&euro;={{ cart.final_price  | currency }}&euro;

                          </li>
                        </ul>
                      </span>
               </div>           
              </section>

           <!--  
                <section id="about" class="about" v-if="modalContent.entity!=null && modalContent.order!=null && modalContent.order.order_type!=null">

<div class="content container aos-init aos-animate" data-aos="fade-up" :class="modalContent.order.order_type.name">



<div class="row" v-if="articles_order.article.codice!=null">
<div class="col-4 col-label">Codice</div>
<div class="col-8">{{ articles_order.article.codice }}</div>
</div>

<div class="row">
<div class="col-4 col-label">Prezzo</div>
<div class="col-8">
 {{ articles_order.price | currency }}

  <span v-if="articles_order.price_pre_discount!='' && articles_order.price_pre_discount>0">
    <del>
      {{ articles_order.price_pre_discount | currency }}
    </del>

    <span class="price-promotion"></span>
  </span> 

</div>
</div>

<div class="row">
  <div class="col-4 col-label">Pezzi confezione</div>
  <div class="col-8">{{ articles_order.conf }}</div>
  </div>

<div class="row">
  <div class="col-4 col-label">Unità di misura di riferimento</div>
  <div class="col-8">{{ articles_order.um_rif_label }}</div>
</div>

<div class="row" v-if="articles_order.qta_multipli > 1">
  <div class="col-4 col-label">Multipli</div>
  <div class="col-8">{{ articles_order.qta_multipli }}</div>
</div>

<div class="row" v-if="articles_order.qta_minima > 0">
  <div class="col-4 col-label">Quantità minima</div>
  <div class="col-8">{{ articles_order.qta_minima }}</div>
</div>

<div class="row" v-if="articles_order.qta_massima > 0">
  <div class="col-4 col-label">Quantità massima</div>
  <div class="col-8">{{ articles_order.qta_massima }}</div>
</div>

<div v-if="modalContent.order.order_type.code=='PROMOTION'">
    <div class="row" v-if="articles_order.qta_massima_order > 0">
      <div class="col-4 col-label">Promozione valida</div>
      <div class="col-8">se sull'ordine totale si raggiungerà la quantità di <strong>{{ articles_order.qta_massima_order }}</strong> acquisti</div>
    </div>
</div>   

 <div v-if="modalContent.order.order_type.code!='PROMOTION'">
    <div class="row" v-if="articles_order.qta_minima_order > 0">
      <div class="col-4 col-label">Quantità minima rispetto all'ordine</div>
      <div class="col-8">{{ articles_order.qta_minima_order }}</div>
    </div>
    <div class="row" v-if="articles_order.qta_massima_order > 0">
      <div class="col-4 col-label">Quantità massima rispetto all'ordine</div>
      <div class="col-8">{{ articles_order.qta_massima_order }} (acquistati ora {{ articles_order.qta_cart }})</div>
    </div>
 </div>


<div class="row" v-if="articles_order.stato != 'Y'">
<div class="col-4 col-label">Stato</div>
<div class="col-8">{{ articles_order.stato }}</div>
</div>

<div class="row" v-if="articles_order.article.nota!=null">
<div class="col-4 col-label">Nota</div>
<div class="col-8" v-html="$options.filters.html(articles_order.article.nota)"></div>
</div>

<div class="row" v-if="articles_order.article.ingredienti!=null">
<div class="col-4 col-label">Ingredienti</div>
<div class="col-8" v-html="$options.filters.html(articles_order.article.ingredienti)"></div>
</div>
       
<div class="row" v-if="modalContent.order.suppliers_organization.frequenza!=null">
<div class="col-4 col-label">Ordine con frequenza</div>
<div class="col-8">{{ modalContent.order.suppliers_organization.frequenza }}</div>
</div> 

<div class="row" v-if="modalContent.order.referents!=null">
<div class="col-4 col-label">Referenti</div>
<div class="col-8">

    <dl class="row">
      <template v-for="(referent, index) in modalContent.order.referents">
          <dt class="col-sm-3">
            <span v-if="referent.type!='referente'">{{referent.type}} </span>
            {{ referent.name }}
          </dt>
          <dd class="col-sm-9">
            <span v-if="referent.email!=''"><a :href="'mailto:'+referent.email">{{referent.email}}</a></span>
            <span v-if="referent.phone_satispay!=null">{{referent.phone_satispay}} <img src="/img/satispay-ico.png" title="il referente ha Satispy" /></span>
            <span v-if="referent.phone!=null">{{referent.phone}}</span>
          </dd>
      </template>
    </dl>
</div>
</div>

<form v-if="modalContent.cart!=null && modalContent.cart.hasFieldCartNote=='Y'">
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="cart-note">Nota per il referente</label>
      <textarea cols="100" rows="10" id="cart-nota" name="cart-nota" ref="cart-nota" class="form-control" 
        v-model="modalContent.cart.nota">
      </textarea>

    </div>
  </div>
</div>

<div v-if="modalContent.msg!=null && modalContent.msg!=''" class="alert alert-info">{{ modalContent.msg }}</div>

<button type="button" v-on:click="sendCartNota()" class="btn btn-primary">Invia nota al referente

  <span v-if="isLoading" class="box-spinner"> 
    <div class="spinner-border text-info" role="status">
      <span class="sr-only">Loading...</span>
    </div>  
  </span>
</button>
</form>

</div>
</section>
-->




              </div> <!-- modal-body --> 
              <div class="modal-footer">
                {{modalContent.footer}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal()">Chiudi</button>
              </div>
            </div> <!-- modal-content -->
          </div>

      </div>
    </transition>
    <transition name="fade">
        <mask-component v-show="showModalArticleOrdersCart"/>
    </transition>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import mask from "./Mask.vue";

export default {
  name: "app-modal-article-orders-cart",
  components: {
    maskComponent: mask,
  },
  data() {
    return {
      isLoading: false
    };
  },
  computed: {
    ...mapGetters({
      showModalArticleOrdersCart: "getShowModalArticleOrdersCart",
      modalContent: "getModalContent"
    })
  },
  methods: {
    ...mapActions(['showOrHiddenModalArticleOrdersCart']),
    closeModal() {
      this.showOrHiddenModalArticleOrdersCart();
    },
    sendCartNota() {

      var _this = this;

      /*
       * se gestisco textarea con v-model=nota nno si puo' settare il valore!
       */
        // var nota = this.$refs["cart-nota"].value;
        let nota = this.modalContent.cart.nota;
        // console.log(nota, 'sendCartNota');
        // console.log(this.articles_order.ids);

        _this.modalContent.msg = null;
        _this.isLoading=true;

        let params = {
          order_id: this.articles_order.ids.order_id,
          article_organization_id: this.articles_order.ids.article_organization_id,
          article_id: this.articles_order.ids.article_id,
          nota: nota
        };
 
        let url = "/admin/api/carts/setNota";
        axios
          .post(url, params)
          .then(response => {
              
              // _this.$refs["cart-nota"].value = nota;

              /*console.log(response.data);*/
              if(typeof response.data !== "undefined") { 

                if(typeof response.data.msg !== "undefined") {
                   _this.modalContent.msg = response.data.msg;
                } 
              }

              _this.isLoading=false;
          })
          .catch(error => {
            this.isLoading=false;
            console.error("Error: " + error);
          });
    }
  },
  filters: {
      currency(amount) {
        let locale = window.navigator.userLanguage || window.navigator.language;
          locale = 'it-IT';
        const amt = Number(amount);
        return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
      },
      formatDate(value) {
        if (value) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          locale = 'it-IT';
          /* console.log(locale); */
          moment.toLocaleString(locale)
          moment.locale(locale);
          return moment(String(value)).format('DD MMMM YYYY')
        }
      },
      counter: function (index) {
          return index+1
      },
      html(text) {
      console.log('html '+text);
          return text;
      },
  }  
};
</script>

<style scoped>
.modal-wrapper {
  width: 100%;
  height: 300px;
  box-sizing: border-box;
  padding: 1em;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* background-color: #fff; */
  box-shadow: 0 0 10px rgba(144,144,144,.2);
  border: 0;
  border-radius: 5px;
  line-height: 1.5em;
  opacity: 1;
  transition: all .5s;
  z-index: 2;
  min-height: calc(100% - (1.75rem * 2));
}
.modal-body {
  overflow-y: auto;
  height: 400px;
}
.modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
}
@media (min-width: 576px) {
  .modal-dialog-centered {
      min-height: calc(100% - (1.75rem * 2));
  }  
}
@media (min-width: 1200px) {
  .modal-xl {
      max-width: 1140px;
  }  
}
@media (min-width: 992px) {
  .modal-lg, .modal-xl {
      max-width: 800px;
  }  
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

.content.type-PROMOTION {
  background-image: url("/img/promotion-100w-110h.png");
  background-repeat: no-repeat, no-repeat;
  background-position: right top;
} 
@media screen and (max-width: 600px) {
  .content.type-PROMOTION {
    background-image: url("/img/promotion-50w-55h.png");
    background-repeat: no-repeat, no-repeat;
    background-position: right top;
  } 
}
.price-promotion {
  padding: 25px;
  margin-left: 15px;  
  background-image: url("/img/promotion-50w-55h.png");
  background-repeat: no-repeat, no-repeat;
  background-position: right center;
}
</style>
