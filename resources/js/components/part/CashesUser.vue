<template>

<div class="modal fade" id="cashesUserModal" tabindex="-1" role="dialog" aria-labelledby="cashesUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cashesUserModalLabel">Situazione cassa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">


          <div class="btn-group">
             
              <div v-if="datas.user_cash_e!=null && datas.user_cash < 0" 
                    class="alert alert-danger" 
                    v-html="$options.filters.debito_cassa(datas.user_cash_e)">
              </div>

              <div v-if="datas.user_cash_e!=null && datas.user_cash >= 0" 
                    class="alert alert-primary" 
                    v-html="$options.filters.credito_cassa(datas.user_cash_e)">
              </div>

              <div v-if="datas.user_cash_e!=null && datas.ctrl_limit.fe_msg!=null" class="alert alert-warning" v-html="$options.filters.html(datas.ctrl_limit.fe_msg)"></div>

              <div v-if="datas.user_cash_e!=null && datas.ctrl_limit.fe_msg_tot_acquisti != ''" 
                    class="alert alert-warning" 
                    v-html="$options.filters.html(datas.ctrl_limit.fe_msg_tot_acquisti)">
              </div>

              <div v-if="datas.user_cash_e!=null && datas.ctrl_limit.has_fido" 
                    class="alert alert-info" 
                    v-html="$options.filters.fido(datas.ctrl_limit.importo_fido_e)">
              </div>

          </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>




</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "casches-user",
  data() {
    return {
      datas: {
        user_cash_e: null,
        ctrl_limit: {
          fe_msg: null,
          fe_msg_tot_acquisti: null
        }
      }
    };
  },
  mounted() {
    this.get();
  },
  computed: {
    ...mapGetters(["cashesUserReload"]),
  }, 
  watch: {
    /*
     * carica i dati in base all'url settato nel tabs e lo passa al componente
     * se justLoading.includes(this.url) i dati del tab sono gia' stati caricati
     */
    cashesUserReload (newValue, oldValue) {
      console.log('cashesUserReload '+newValue+' - '+oldValue);
      
      if(newValue)
        this.get();

      this.cashesUserReloadFinish();
    }
  },   
  methods: {
    ...mapActions(["cashesUserReloadFinish"]),
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
    fido(text) {
        return "Fido di "+text;
    },
    html(text) {
        return text;
    }
  }  
};
</script>

<style scoped>
</style>