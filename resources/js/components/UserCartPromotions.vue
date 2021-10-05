<template>

	<main>

        <div class="box-btn-pdf">
           <a href="/admin/api/exports/user-promotion-cart/" target="_blank" title="Stampa carrello della promozione" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Stampa carrello delle promozioni</a>
        </div>

	   <div v-for="(promotion, index) in results.promotions"
	            :promotion="promotion"
	            :key="promotion.promotion.id"
	    >
			
     		<div class="row">
            <div class="col-sm-12 col-xs-12 col-md-12"> 

              <div class="card mb-3">

                <div class="row no-gutters">
                  <div class="col-md-2"> 
                      <div class="content-img-supplier">
                        <img v-if="promotion.promotion.organization.suppliers_organization.supplier.img1 != ''"
                          class="img-supplier" :src="appConfig.$siteUrl+'/images/organizations/contents/'+promotion.promotion.organization.suppliers_organization.supplier.img1"
                          :alt="promotion.promotion.organization.suppliers_organization.supplier.name">
                      </div>

                  </div>
                  <div class="col-md-10">
                     <div class="card-body type-PROMOTION">
                        <h5 class="card-title">
                            <a v-if="promotion.promotion.organization.suppliers_organization.supplier.www!=''" target="_blank" v-bind:href="promotion.promotion.organization.suppliers_organization.supplier.www" title="vai al sito del produttore">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </a>
                            <span v-if="promotion.promotion.organization.suppliers_organization.supplier.www==''">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </span>
                            <small class="card-text">
                              {{ promotion.promotion.organization.suppliers_organization.supplier.descrizione }}
                            </small>                        
                        </h5>

                        <p v-if="promotion.promotion.id!=null" class="card-text">
                            <b>{{ promotion.promotion.name }}</b>
                            terminerà {{ promotion.promotion.data_fine | formatDate }}
                        </p>

                     </div> <!-- card-body -->
                     <div class="card-footer text-muted bg-transparent-disabled">
                        <strong>Note per la consegna e il pagamento</strong> 
                        <div v-html="$options.filters.html(promotion.promotion.nota)"></div>
                     </div>  <!-- card-footer --> 

                  </div> <!-- col-md-10 -->
                </div> <!-- row -->
        
              </div> <!-- card -->

            </div> <!-- col... -->
          </div> <!-- row -->

	        <user-cart-articles 
      			:order="promotion.order" 
      			:article_orders="promotion.article_orders"
      			></user-cart-articles>

  
    </div> <!-- loop -->

    <div v-if="results.promotions==null || results.promotions.length==0" class="alert alert-warning">
        Nessuna promozione per te
    </div>

	</main>

</template>

<script>
import { mapActions } from "vuex";
import UserCartArticles from "../components/part/UserCartArticles.vue";

export default {
  name: "user-cart-promotions",
  /*
   * results: {
   * 		delivery_id: null,
   *   	orders: [],
   *  	promotions: []
   * },
   */
  props: {
    results: {}
  },
  components: {
    UserCartArticles
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
      lowerCase : function(value) {
        return value.toLowerCase().trim();
      },
      html(text) {
        return text;
      },
   }
};
</script>

<style scoped>
.row-gray {
    background-color: #dee2e6;
    display: block;
    padding: 10px;
}
.box-btn-pdf {
	text-align: right;
	width: 100%;
	margin-bottom: 5px;
}
.box-btn-pdf a {
	font-size: 22px;
}
.box-order {
	clear: both;
}
</style> 