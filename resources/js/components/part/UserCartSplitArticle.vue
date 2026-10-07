<template>

  <main>

    <div class="row">
      <div class="col-2 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
        <div class="content-img-article-small">
            <img v-if="article_order.img1!=''" class="img-article-small responsive" :src="article_order.img1" :alt="article_order.name">
            <div v-if="article_order.is_bio" class="box-bio">
                <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
            </div>
          </div>        
      </div>
      <div class="col-text col-3 col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block">
        {{ article_order.name }}
        <div><small v-html="$options.filters.html(article_order.descri)"></small></div> 
      </div>
      <div class="col-text col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Conf. 
             </span> 
              {{ article_order.conf }}
        </div>
        <div class="col-text col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo
             </span>
              {{ article_order.price | currency }} &euro;
                <del v-if="article_order.price_pre_discount != null"
                    >{{ article_order.price_pre_discount | currency }} &euro;</del
                  > 
        </div>
        <div class="col-text col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo/UM 
             </span>           
              {{ article_order.um_rif_label }}
        </div>
        <div class="col-text text-center col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">          
              {{ article_order.cart.final_qta }} 
        </div>
        <div class="col-text col-3 col-sm-3 col-md-2 col-lg-3 col-xs-3 d-none d-md-block d-lg-block d-xl-block"> 
              <div v-html="totaleCartQtaSplits(article_order)"></div>
             
              <b-button block variant="primary" @click="split(article_order)">
                <svg 
                    xmlns="http://w3.org" 
                    width="24" 
                    height="24" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round"
                  >
                    <circle cx="6" cy="6" r="3"></circle>
                    <circle cx="6" cy="18" r="3"></circle>
                    <line x1="9.8" y1="8.2" x2="22" y2="20.4"></line>
                    <line x1="9.8" y1="15.8" x2="22" y2="3.6"></line>
                  </svg> Suddividi la quantità
              </b-button>

        </div>      
    </div>

    <!-- splits -->
    <div class="row" v-for="cart_split in article_order.cart_splits" :key="'cart_split-'+cart_split.id">
      <div class="col-2 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-3 col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-3 col-sm-1 col-md-1 col-lg-3 col-xs-5 d-none d-md-block d-lg-block d-xl-block">
          
          <input
            type="text"
            class="form-control text-left"
            v-model="cart_split.name"
            placeholder="Famiglia/amico"
            title="nominativo"
          />          

        </div>
        <div class="col-3 col-sm-3 col-md-2 col-lg-3 col-xs-3 d-none d-md-block d-lg-block d-xl-block">

          <div class="quantity buttons_added">

            <input type="button" value="-"
              class="minus"
              @click="minusCart(cart_split)"
              :disabled="false" />

            <input
              type="text"
              class="form-control text-center"
              v-if="article_order!=null"
              :value="cart_split.qta + ' di ' + article_order.cart.final_qta"
              :disabled="true"
              min="0"
              max="article_order.cart.final_qta"
              size="4"
              inputmode="numeric"
              title="Quantità"
            />

            <input type="button" value="+" 
                class="plus" 
                v-if="article_order!=null"
                @click="plusCart(cart_split, article_order.cart.final_qta)" 
                max="article_order.cart.final_qta"
                :disabled="false" />

          </div> <!-- quantity buttons_added -->          

          <!-- trasport {{ cart_split.trasport }} -->
           
        </div>
      </div>

  </main>

</template>

<script>
import  { rndMixin } from '../../mixins/rndMixin.js';

export default {
  name: "user-cart-split-article",
  props: ['article_order'],
  data() {
    return {
      isLoading: false
    };
  },
  components: {
   
  },
  methods: {
    minusCart(cart_split) {
      if(cart_split.qta>0) {
        cart_split.qta--;
        this.emitUpdate();
      }
    },
    plusCart(cart_split, final_qta) {
      if(cart_split.qta<final_qta) {
        cart_split.qta++;
        this.emitUpdate();
      }
    },
    split(article_order) {
      let new_split = {
          id: 'NEW-'+rndMixin(),  
          name: "",
          qta: 0
      };

      article_order.cart_splits.push(new_split);

      this.emitUpdate();
    },
    emitUpdate() {
      this.$emit('emitUpdate', this.article_order);
    }
  }, 
  computed: {
    totaleCartQtaSplits() {
      return (article_order) => {
          let results = '';
          if(typeof article_order==='undefined' || article_order.cart_splits.length==0)
            return results;

          let totale = 0; 
          article_order.cart_splits.forEach((cart_split) => {
            const qta = Number(cart_split.qta);
            if (!isNaN(qta)) {
              totale += qta;
            }

          });

          if(totale>article_order.cart.final_qta)
            results = "<div class='alert alert-danger'>La quantità totale accede di "+ (-1 * (article_order.cart.final_qta - totale)) +"!</div>";
          else
          if(totale<article_order.cart.final_qta) {
            let qta = (-1 * (totale - article_order.cart.final_qta));
            if(qta == 1)
              results = "<div class='alert alert-danger'>Ti manca da suddividere ancora "+ qta +" quantità!</div>";
            else 
              results = "<div class='alert alert-danger'>Ti mancano da suddividere ancora "+ qta +" quantità!</div>";
          }
          else 
            results = '';

          return results;
      }
    },
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
    html(text) {
        return text;
    }    
  }
};
</script>

<style scoped>
.row {
  margin: 5px 0 5px 0;
}
.col-img {
  padding: 0px;
}
.col-text {
  padding: 10px 0px;
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
.card-footer {
    padding: 0.75rem 0.2rem  !important;
}

.buttons_added {
    width: 100%;
    display: inline-flex;
}
.box-spinner {
    margin: 0px;
}
.buttons_added .spinner-border {
    display: inline-table;
    margin: 0 5px;
}
.minus, .plus {
    display: flex;
    align-items: center;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border: 1px solid gray;
    border-radius: 2px;
}
</style>

