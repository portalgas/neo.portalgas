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

                  <div class="content container aos-init aos-animate" data-aos="fade-up" :class="modalContent.order.order.order_type.name">

                    <div class="box-supplier-organization" style="margin-bottom: 15px;">
                        <span class="box-img">
                          <img :src="appConfig.$siteUrl+'/images/organizations/contents/'+modalContent.order.order.suppliers_organization.supplier.img1" :alt="modalContent.order.order.suppliers_organization.supplier.name" :title="modalContent.order.order.suppliers_organization.supplier.name" width="50px" class="img-supplier" />
                        </span> 

                        <span class="box-name" v-if="modalContent.order.order.suppliers_organization.supplier.www!=''">
                            <a :href="modalContent.order.order.suppliers_organization.supplier.www" target="_blank" title="vai al sito del produttore">
                              {{ modalContent.order.order.suppliers_organization.supplier.name }}
                            </a>
                        </span> 

                        <span class="box-name" v-if="modalContent.order.order.suppliers_organization.supplier.www==''">
                            {{ modalContent.order.order.suppliers_organization.supplier.name }}
                        </span> 
                    </div>

                    <h2>
                      <strong>Consegna</strong>
                      <span v-if="modalContent.order.order.delivery.sys=='Y'">
                          {{ modalContent.order.order.delivery.luogo }}
                      </span>
                      <span v-if="modalContent.order.order.delivery.sys!='Y'">
                          {{ modalContent.order.order.delivery.luogo }} il {{ modalContent.order.order.delivery.data | formatDate }}
                      </span>
                    </h2>

                    <div v-if="modalContent.order.order.order_type_id!=9 && modalContent.order.order.hasTrasport=='N'" class="badge badge-secondary">Non ha spese di trasporto</div>
                    <div v-if="modalContent.order.order.order_type_id!=9 && modalContent.order.order.hasTrasport=='Y'" class="badge badge-warning">Ha spese di trasporto</div>

                    <div v-if="modalContent.order.order.order_type_id!=9 && modalContent.order.order.hasCostMore=='N'" class="badge badge-secondary">Non ha costi aggiuntivi</div>
                    <div v-if="modalContent.order.order.order_type_id!=9 && modalContent.order.order.hasCostMore=='Y'" class="badge badge-warning">Ha costi aggiuntivi</div>
                          

                    <div class="badge badge-pill" :class="'text-color-background-'+modalContent.order.order.order_state_code.css_color" :style="'background-color:'+modalContent.order.order.order_state_code.css_color">{{ modalContent.order.order.order_state_code.name }}</div>

                    <p v-if="modalContent.order.order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ modalContent.order.order.order_type.descri }}</p>

                    <p v-for="(articles_order, index) in modalContent.order.articles_orders">
                        <ul v-if="articles_order.carts.length>0" v-for="(cart, index) in articles_order.carts" style="margin-top:25px;list-style-type: none;"> 
                          <li>

                            <div class="row" v-if="articles_order.is_bio!='' || articles_order.img1!=''">
                              <div class="col-md-2">
                                <span class="box-img" v-if="articles_order.img1!=''">
                                    <img :src="articles_order.img1" class="img-article" width="100px" />
                                </span>
                              </div>
                              <div class="col-md-9">
                                <b>{{ articles_order.name }}</b> - {{ cart.final_qta }} di {{ articles_order.conf }} <!-- * {{ articles_order.price| currency }}&euro;--> {{ cart.final_price| currency }}&euro;
                              </div>
                              <div class="col-md-1 col-label">
                                  <span class="box-bio" v-if="articles_order.is_bio!=''">
                                    <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
                                  </span>
                              </div>
                            </div>

                          </li>
                        </ul>
                    </p>

               </div>           
              </section>




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
