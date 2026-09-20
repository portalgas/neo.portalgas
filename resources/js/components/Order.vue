<template>
    <main v-if="!isRunOrders">
        <div class="content-img-supplier">
			      <img style="max-width:150px" v-if="order.suppliers_organization.supplier.img1 != ''" 
                    class="img-supplier" 
                    :src="appConfig.$siteUrl+'/images/organizations/contents/'+order.suppliers_organization.supplier.img1"
                    :alt="order.suppliers_organization.name">
        </div>

        {{ order.suppliers_organization.name }}

        <span class="d-none d-md-inline-block d-lg-inline-block d-xl-inline-block">
            <span v-if="order.order_state_code.code=='OPEN-NEXT'">- aprirà {{ order.data_inizio | formatDate }} </span>
            <span v-if="order.order_state_code.code=='OPEN'">- chiuderà {{ order.data_fine | formatDate }}</span>
            <span v-if="order.order_state_code.code=='OPEN-NEXT' && order.order_state_code.code!='OPEN'">- data chiusura {{ order.data_fine | formatDate }}</span>
            <span v-if="order.order_state_code.code=='RI-OPEN-VALIDATE'">- riaperto fino al {{ order.data_fine_validation | formatDate }} per completare i colli</span>
        </span>

        <span class="badge badge-pill" :class="'text-color-background-'+order.order_state_code.css_color" :style="'background-color:'+order.order_state_code.css_color">{{ order.order_state_code.name }}</span>
        
        <span v-if="order.order_type.name!='GAS'" class="badge badge-pill badge-primary">{{ order.order_type.descri }}</span>  
        
        <div v-if="order.nota!=null && order.nota.trim()!=''" class="col-10 alert alert-info ml-auto mr-1 no-decoration" 
            v-html="$options.filters.html(order.nota)">
        </div>

        <div v-if="order.order_type_id!=9 && order.hasTrasport=='N'" class="badge badge-secondary">Non ha spese di trasporto</div>
        <div v-if="order.order_type_id!=9 && order.hasTrasport=='Y'" class="badge badge-warning">Ha spese di trasporto</div>

        <div v-if="order.order_type_id!=9 && order.hasCostMore=='N'" class="badge badge-secondary">Non ha costi aggiuntivi</div>
        <div v-if="order.order_type_id!=9 && order.hasCostMore=='Y'" class="badge badge-warning">Ha costi aggiuntivi</div>

        <div v-if="order.order_type_id!=9 && order.hasCostLess=='N'" class="badge badge-secondary">Non ha sconti aggiuntivi</div>
        <div v-if="order.order_type_id!=9 && order.hasCostLess=='Y'" class="badge badge-warning">Ha sconti aggiuntivi</div>
        
    </main>
</template>

<script>
import axios from "axios";

export default {
  name: "app-order",
  props: ['order_type_id', 'order_id'],
  data() {
    return {
      isRunOrders: false,
      order: {
        suppliers_organization: {
          supplier: {
            img1: null
          }
        },
        order_state_code: {},
        order_type: {}
      },
    }
  },
  mounted() {
    this.getOrder();
  },
  methods: {
    getOrder() {

        this.isRunOrder = true;

        let url = "/admin/api/orders/get";

        let params = {
        order_type_id: this.order_type_id,
        order_id: this.order_id
        };

        axios
        .post(url, params)
        .then(response => {

            this.isRunOrder = false;

            // console.log(response.data);
            if(typeof response.data !== "undefined") {
                this.order = response.data;
            }
            else {
                console.error("Error: " + response.message);
                this.isRunOrder = false;
            }
        })
        .catch(error => {
            this.isRunOrder = false;
            console.error("Error: " + error);
        });
        },
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
}
</script>


