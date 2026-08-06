<template>
    <main style="text-align: right;margin-top:5px;">

        <div v-if="order.order_final_price_only_carts===order.order_final_price">
            <div class="footer col-sm-12 col-xs-12 col-md-12 alert alert-info ">Totale {{ order.order_final_price| currency }} &euro;</div>
        </div>
        <div v-else>
            <div class="row">
                <div class="footer col-sm-12 col-xs-12 col-md-12">Acquisti {{ order.order_final_price_only_carts| currency }} &euro;
                    <span v-if="order.summary_order_aggregate!=null && order.summary_order_aggregate.importo!=null">*</span>
                </div>
            </div>
            <div class="row" v-if="order.trasport!=0.00 && order.summary_order_trasport!=null && order.summary_order_trasport.importo_trasport!=null">
                <div class="footer col-sm-12 col-xs-12 col-md-12">Trasporto {{ order.summary_order_trasport.importo_trasport| currency }} &euro;</div>
            </div>
            <div class="row" v-if="order.cost_more!=0.00 && order.summary_order_cost_more!=null && order.summary_order_cost_more.importo_cost_more!=null">
                <div class="footer col-sm-12 col-xs-12 col-md-12">Costo {{ order.summary_order_cost_more.importo_cost_more| currency }} &euro;</div>
            </div>
            <div class="row" v-if="order.cost_less!=0.00 && order.summary_order_cost_less!=null && order.summary_order_cost_less.importo_cost_less!=null">
                <div class="footer col-sm-12 col-xs-12 col-md-12">Sconto {{ order.summary_order_cost_less.importo_cost_less| currency }} &euro;</div>
            </div>
            <div class="row">
                <div class="footer col-sm-12 col-xs-12 col-md-12 alert alert-info ">Totale {{ order.order_final_price| currency }} &euro;</div>
            </div>
        </div>

    </main>
</template>
  
<script>  
  export default {
    name: "order-totals",
    props: ['order'],
	filters: {
    	currency(amount) {
	      let locale = window.navigator.userLanguage || window.navigator.language;
          locale = 'it-IT';
	      const amt = Number(amount);
	      return amt && amt.toLocaleString(locale, {minimumFractionDigits: 2, maximumFractionDigits:2}) || '0'
	    }
    }    
  };
</script>
  