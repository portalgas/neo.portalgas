<template>

<div class="btn-group">
    
    <div v-if="datas.user_cash < 0" 
          class="alert alert-danger" 
          v-html="$options.filters.debito_cassa(datas.user_cash_e)">
    </div>

    <div v-if="datas.user_cash >= 0" 
          class="alert alert-primary" 
          v-html="$options.filters.credito_cassa(datas.user_cash_e)">
    </div>

    <div class="alert alert-warning" v-html="$options.filters.html(datas.ctrl_limit.fe_msg)"></div>

    <div v-if="datas.ctrl_limit.fe_msg_tot_acquisti != ''" 
          class="alert alert-warning" 
          v-html="$options.filters.html(datas.ctrl_limit.fe_msg_tot_acquisti)">
    </div>

</div>

</template>

 <script>
export default {
  name: "casches-user",
  data() {
    return {
      datas: []
    };
  },
  mounted() {
    this.get();
  },
  methods: {
    get () {

      let url = "/admin/api/users/cash-ctrl-limit";
      axios
        .post(url)
        .then(response => {
            console.log(response.data);
            if(typeof response.data !== "undefined") {
              this.datas = response.data;
            }
        })
        .catch(error => {
          this.isRunDeliveries=false;
          console.error("Error: " + error);
        });
    },  
  },
  filters: {
    debito_cassa(text) {
        return "Debito verso la cassa "+text;
    },
    credito_cassa(text) {
        return "Credito verso la cassa "+text;
    },
    html(text) {
        return text;
    }
  }  
};
</script>

<style scoped>
</style>