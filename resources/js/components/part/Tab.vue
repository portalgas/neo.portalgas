<template>
    <div class="tab" v-show="isActive">

        <div v-if="isLoading" class="box-spinner"> 
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>  
        </div>

        <!-- slot v-if="!isLoading" :datas="datas"></slot-->

        <orders-deliveries v-if="url=='/admin/api/deliveries/gets'" :datas="datas"></orders-deliveries> 

        <orders-suppliers v-if="url=='/admin/api/orders/gets'" :datas="datas"></orders-suppliers> 

    </div>
</template>

<script>
import OrdersDeliveries from '../../components/OrdersDeliveries.vue';
import OrdersSuppliers from '../../components/OrdersSuppliers.vue';

export default {
    props: {
        name: { required: true },
        url: { required: true },
        selected: { default: false}
    },
    data() {
        return {
            datas:  {},
            isActive: false,
            isLoading: false
        };  
    },
  components: {
    OrdersDeliveries,
    OrdersSuppliers
  },      
    computed: {  
        href() {
            return '#' + this.name.toLowerCase().replace(/ /g, '-');
        }
    },
    watch: {
        isLoading (newValue, oldValue) {
            console.log('tab.watch isLoading ['+this.name+'] '+newValue+' - '+oldValue);
            if(newValue===true) {

                this.datas = {};

                let url = this.url;
                console.log('tab.watch url '+url);

                axios
                .post(url)
                .then(response => {

                    this.isLoading=false;

                    console.log(response.data);
                    if(typeof response.data !== "undefined") {
                        this.datas = response.data;
                        // console.log(this.datas);                    
                    }
                })
                .catch(error => {
                  this.isLoading=false;
                  console.error("Error: " + error);
                });

            }
        }
    },      
    mounted() {
        this.isActive = this.selected; 
        this.isLoading = this.selected;  
        console.log('tab.mounted ['+this.name+'] isActive '+this.isActive+' isLoading '+this.isLoading); 
    }
};
</script>
