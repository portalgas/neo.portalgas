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
                   @click="getCashHistoryByUser"  
                   class="alert alert-primary cursor-pointer"> 
                <i class="fas fa-search"></i>
                <span v-html="$options.filters.credito_cassa(datas.user_cash_e)"></span>
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

          <!--  cash history  -->
          <!--  cash history  -->
          <!--  cash history  -->
          <div v-if="isRunCashHistory" class="box-spinner"> 
            <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
            </div>    
          </div>

          <div v-if="dataNotFound===false" class="alert alert-warning">
            Non ci sono voci in cassa precedenti
          </div>
          <div v-if="cashHistories!=null" class="table-responsive">
            <table class="table table-striped">
            <thead><tr>
                <th>N</th>
                <th colspan="2">Saldo</th>
                <th>Operazione</th>
                <th>Nota</th>
                <th>Inserito</th>
            </tr></thead><tbody>
            <tr  
              v-for="(cashHistory, index) in cashHistories"
              :key="cashHistory.id">
                  <td>{{ index + 1 }}</td>
                  <td 
                    style="width:10px;" 
                    :style="'background-color:'+cashHistory.color_alert"></td>         
                  <td v-html="$options.filters.html(cashHistory.importo_e)">
                  </td>
                  <td>
                    <span v-if="cashHistory.operazione>0">+ </span>
                    <span v-html="$options.filters.html(cashHistory.operazione_e)"></span>
                  </td>
                  <td v-html="$options.filters.html(cashHistory.nota)">
                  </td>
                  <td style="white-space: nowrap;">
                    {{ cashHistory.modified | formatDate }}
                  </td>
            </tr>
            </tbody></table>
          </div>



          <!--  link  -->
          <!--  link  -->
          <!--  link  -->
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <a :href="'/admin/joomla25Salts?scope=FE&c_to=/home-'+j_seo+'/my-profile'" class="btn btn-blue">Visualizza il tuo profilo / Modifica le impostazioni</a>
            </li>
            <li class="list-group-item">
              <a :href="'/admin/joomla25Salts?scope=FE&c_to=/home-'+j_seo+'/bookmarks-mails'"
               class="btn btn-blue">Personalizza le mail</a>
            </li>
            <li class="list-group-item">
              <a :href="'/admin/joomla25Salts?scope=FE&c_to=/home-'+j_seo+'/carts-history'" 
              class="btn btn-blue">Storico acquisti</a>
            </li>
          </ul>

      </div>
      <div class="modal-footer">
        <a :href="portalgasFeUrl+'/login'" class="mr-auto"><button type="button" class="btn btn-danger mr-auto">Logout</button></a>
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
      j_seo: '',
      organizationTemplatePayToDelivery: '',
      datas: {
        user_cash_e: null,
        ctrl_limit: {
          fe_msg: null,
          fe_msg_tot_acquisti: null
        }
      },
      isRunCashHistory: false,
      dataNotFound: '',
      cashHistories: null
    };
  },
  mounted() {
    this.getGlobals();
    this.getCashCtrlLimit();
  },
  computed: {
    ...mapGetters(["cashesUserReload"]),
    portalgasFeUrl() {
      var host = window.location.host.toLowerCase();
      var glob = {};

      switch(host) {
        case 'neo.portalgas.local.it:81':
          glob = require('../../../config/development.env');
        break;
        case 'neotest.portalgas.it':
          glob = require('../../../config/test.portalgas.env');
          break;
        case 'neonext.portalgas.it':
          glob = require('../../../config/next.portalgas.env');
          break;
        default:
          glob = require('../../../config/production.env');
      }

      // console.log('host '+host+' portalgasFeUrl '+glob.PortalgasFeUrl);

      return glob.PortalgasFeUrl;
    }
  }, 
  watch: {
    /*
     * carica i dati in base all'url settato nel tabs e lo passa al componente
     * se justLoading.includes(this.url) i dati del tab sono gia' stati caricati
     */
    cashesUserReload (newValue, oldValue) {
      // console.log('cashesUserReload '+newValue+' - '+oldValue);
      
      if(newValue)
        this.getCashCtrlLimit();

      this.cashesUserReloadFinish();
    }
  },   
  methods: {
    ...mapActions(["cashesUserReloadFinish"]),
    getGlobals() {
      /*
       * variabile che arriva da cake, dichiarata come variabile in Layout/vue.ctp, in app.js settata a window. 
       * recuperata nei components con getGlobals()
       */
      this.j_seo = window.j_seo;
      this.organizationTemplatePayToDelivery = window.organizationTemplatePayToDelivery;
    },     
    getCashHistoryByUser() {

      this.cashHistories = null;
      this.dataNotFound = '';
      this.isRunCashHistory=true;

      let url = "/admin/api/cashes/cash-history-by-user";
      // console.log('getCashHistoryByUser '+url);
      axios
        .post(url)
        .then(response => {
            // console.log(response.data);
            if(typeof response.data !== "undefined") {
              this.cashHistories = response.data;
              this.dataNotFound = true;
            }
            else
              this.dataNotFound = false;

            this.isRunCashHistory=false;
        })
        .catch(error => {
          this.isRunCashHistory=false;
          console.error("Error: " + error);
        });
    },    
    getCashCtrlLimit() {

      let url = "/admin/api/users/cash-ctrl-limit";
      axios
        .post(url)
        .then(response => {
            // console.log(response.data);
            if(typeof response.data !== "undefined") {
              this.datas = response.data;
            }
        })
        .catch(error => {
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
  }  
};
</script>

<style scoped>
</style>