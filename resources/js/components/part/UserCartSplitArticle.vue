<template>

  <main>

    <div class="row">
      <div class="col-2 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
        <div class="content-img-article-small">
            <img v-if="cart.img1!=''" class="img-article-small responsive" :src="cart.img1" :alt="cart.name">
            <div v-if="cart.is_bio" class="box-bio">
                <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
            </div>
          </div>        
      </div>
      <div class="col-text col-3 col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block">
        {{ cart.name }}
        <div><small v-html="$options.filters.html(cart.descri)"></small></div> 
      </div>
      <div class="col-text col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Conf. 
             </span> 
              {{ cart.conf }}
        </div>
        <div class="col-text col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo
             </span>
              {{ cart.price | currency }} &euro;
                <del v-if="cart.price_pre_discount != null"
                    >{{ cart.price_pre_discount | currency }} &euro;</del
                  > 
        </div>
        <div class="col-text col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
            <span class="d-xl-none d-lg-none d-md-none"> 
              Prezzo/UM 
             </span>           
              {{ cart.um_rif_label }}
        </div>
        <div class="col-text text-center col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">          
              {{ cart.qta_cart }} 
        </div>
        <div class="col-text col-3 col-sm-3 col-md-2 col-lg-3 col-xs-3 d-none d-md-block d-lg-block d-xl-block"> 
              <div v-html="totaleCartQtaSplits(cart)"></div>
             
              <b-button block variant="primary" @click="split(cart)">
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
    <div class="row" v-for="cart_split in cart.cart_splits" :key="'cart_split-'+cart_split.id">
      <div class="col-2 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-3 col-sm-3 col-md-2 col-lg-4 col-xs-3 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-1 col-sm-1 col-md-1 col-lg-1 col-xs-1 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block"></div>
        <div class="col-1 col-sm-2 col-md-2 col-lg-1 col-xs-2 d-none d-md-block d-lg-block d-xl-block">
          
          <input
            type="text"
            class="form-control text-center"
            v-model="cart_split.name"
            placeholder="Famiglia"
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
              :value="cart_split.qta + ' di ' + cart.qta_cart"
              :disabled="true"
              min="0"
              max="cart.qta_cart"
              size="4"
              inputmode="numeric"
              title="Quantità"
            />

            <input type="button" value="+" 
                class="plus" 
                @click="plusCart(cart_split, cart.qta_cart)" 
                max="cart.qta_cart"
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
  props: ['cart'],
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
    plusCart(cart_split, qta_cart) {
      if(cart_split.qta<qta_cart) {
        cart_split.qta++;
        this.emitUpdate();
      }
    },
    split(cart) {
      let new_split = {
          id: 'NEW-'-rndMixin(),  
          name: "",
          qta: 0
      };

      cart.cart_splits.push(new_split);

      this.emitUpdate();
    },
    emitUpdate() {
      this.$emit('emitUpdate', this.cart);
    }
  }, 
  computed: {
    totaleCartQtaSplits() {
      return (cart) => {
          let results = '';
          if(typeof cart==='undefined' || cart.cart_splits.length==0)
            return results;

          let totale = 0; 
          cart.cart_splits.forEach((cart_split) => {
            const qta = Number(cart_split.qta);
            if (!isNaN(qta)) {
              totale += qta;
            }

          });

          if(totale>cart.qta_cart)
            results = "<div class='alert alert-danger'>La quantità totale accede di "+ (-1 * (cart.qta_cart - totale)) +"!</div>";
          else
          if(totale<cart.qta_cart) {
            let qta = (-1 * (totale - cart.qta_cart));
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

