<template>
  <div>

    <transition name="fade">
      <div class="modal-wrapper" v-show="showModalSupplier" tabindex="-1" role="dialog">

          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">{{modalContent.title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi" @click="closeModal()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">


<section id="about" class="about" v-if="modalContent.entity!=null">
    <div class="content container aos-init aos-animate" data-aos="fade-up">

      <!-- I M P O R T -->
    <div v-if="!modalContent.entity.hasOrganization">
        
        <div class="jumbotron jumbotron-fluid" v-if="!importFinish">
          <div class="container">
            <h1 class="display-4">Importalo tra i produttori del tuo G.A.S.</h1>
            <p class="lead">Questo <b>produttore</b> gestisce il proprio listino articoli, quindi il <b>referente</b> dovrà solo aprire e gestire l'ordine! </p>
            <hr class="my-4">
            <p>Clicca sul bottone sottostante per iniziare l'importazione</p>
            
            <p class="lead">
                
                <a v-if="!isImportSupplier" class="btn btn-success btn-block btn-lg cursor-pointer mb-3" @click="clickImport(modalContent.entity.id)" role="button">
                  Importa</a>
            
                <div v-if="isImportSupplier" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 
              </div>

            </p>        
          </div> <!-- container -->
        </div> <!-- jumbotron -->

        <div class="alert alert-success alert-dismissible fade show" role="alert" v-if="importFinish && msg==null">
          <h4 class="alert-heading">Produttore importato</h4>
          <p>
          <ol>
            <li>Contatta il produttore</li>
            <li>Poi potrai accedere a <a href="https://www.portalgas.it/my" target="_blank">https://www.portalgas.it/my</a> e creare un ordine</li>
          </ol>
          <hr>
          <p class="mb-3">Potrai <b>consultare</b> il listino articoli ma non dovrai più preoccuparti di aggiornarlo, ci penserà il produttore!</p>

          <p>
            <a class="btn btn-primary btn-block btn-lg cursor-pointer mb-3" href="/site/produttori" role="button">ricarica la pagina</a>
          </p> 
        </div>

        <div class="alert alert-danger" role="alert" v-if="importFinish && msg!=null">
          {{ msg }}
        </div>

    </div> <!-- !modalContent.entity.hasOrganization -->
    
    <div class="alert alert-danger" role="alert" v-if="modalContent.entity.hasOrganization">
      Il produttore rifornisce già il tuo G.A.S.
    </div>

      <div class="section-title" v-if="modalContent.entity.descrizione">
        <p>{{ modalContent.entity.descrizione }}</p>
      </div>

      <div class="row">
          <div class="col-lg-3 text-center">
              <img v-if="modalContent.entity.img1 != ''"
              class="img-supplier responsive img-fluid"
              :src="modalContent.entity.img1"
              :alt="modalContent.entity.name" />
          </div>
          <div class="col-lg-9 pt-4 pt-lg-0 content">
            <h3 v-if="modalContent.entity.categories_supplier!=null">Categoria: {{ modalContent.entity.categories_supplier.name }}</h3>
            
              <div class="row" v-if="modalContent.entity.nota">
                  <div class="col-lg-12">
                    {{ modalContent.entity.nota }}
                  </div>
              </div> <!-- row -->
              
              <div class="box-more-info">

              <div class="row">
                <div class="col-lg-12">
                    <ul>
                      <li><i class="bi bi-rounded-right"></i> <i class="fas fa-map-marker-alt"></i> {{ modalContent.entity.indirizzo }} {{ modalContent.entity.localita }} <span v-if="modalContent.entity.provincia">({{ modalContent.entity.provincia }})</span> {{ modalContent.entity.cap }}</li>
                      <li v-if="modalContent.entity.www!=''"><i class="bi bi-rounded-right"></i> <i class="fas fa-globe"></i> <a :href="modalContent.entity.www" target="_blank" title="vai al sito del produttore">{{ modalContent.entity.www }}</a></li>
                      <li v-if="modalContent.entity.mail!=''"><i class="bi bi-rounded-right"></i> <i class="fas fa-envelope"></i> <a :href="'mailto:'+modalContent.entity.mail" title="scrivigli una mail">{{ modalContent.entity.mail }}</a></li>
                      <li v-if="modalContent.entity.telefono!=''"><i class="bi bi-rounded-right"></i> <i class="fas fa-phone"></i> {{ modalContent.entity.telefono }}</li>
                      <li v-if="modalContent.entity.telefono2!=''"><i class="bi bi-rounded-right"></i> <i class="fas fa-phone"></i> {{ modalContent.entity.telefono2 }}</li>
                    </ul>
                </div>
              </div> <!-- row -->

              <div v-if="modalContent.entity.voto!=0" v-html="modalContent.entity.voto_html"></div>

           </div>

        </div>  <!-- row -->

          <!--  ARTICLES   -->
          <!--  ARTICLES   -->
          <!--  ARTICLES   -->
          <div v-if="modalContent.entity.articles!=null && modalContent.entity.articles.length>0">

              <h2>Prodotti</h2>


              <div class="row pb-2" 
                v-for="(article, index) in modalContent.entity.articles"
                :article="article"
                :key="article.id"
              >

                  <div class="content-img-article-small col-2">
                    <img v-if="article.img1!=''" class="img-article responsive" :src="article.img1" :alt="article.name">
                    <div v-if="article.is_bio" class="box-bio">
                        <img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica">
                    </div>
                  </div>

                  <div class="col-8">
                      {{ article.name }} <strong>Conf.</strong> {{ article.conf }}
                      <div class="p-2" v-if="article.descri!=null && article.descri!=''" v-html="$options.filters.shortDescription(article.descri)"></div>
                  </div>

                  <div class="col-2" v-if="article.price!=null">
                    <strong>Prezzo</strong> {{ article.price | currency }} &euro;
                  </div>

              </div> <!-- loop -->

          </div>  <!-- v-if -->

    </div>
  </section>

  
              </div> <!-- modal-content -->
              <div class="modal-footer">
                {{modalContent.footer}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal()">Chiudi</button>
              </div>
            </div>
          </div>

      </div>
    </transition>
    <transition name="fade">
        <mask-component v-show="showModalSupplier"/>
    </transition>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import mask from "./Mask.vue";

export default {
  name: "app-modal-supplier-import",
  components: {
    maskComponent: mask
  },
  data() {
    return {
      isImportSupplier: false,
      importFinish: false,
      msg: null
    };
  },  
  mounted() {
  },
  computed: {
    ...mapGetters({
      showModalSupplier: "getShowModalSupplierImport", 
      modalContent: "getModalContent"
    }),
  },
  methods: {
    ...mapActions(['showOrHiddenModalSupplierImport']),
    closeModal() {
      this.isImportSupplier = false;
      this.importFinish = false;  
      this.msg = null;

      this.showOrHiddenModalSupplierImport();
    },  
    clickImport (supplier_id) {

      console.log('clickShowOrHiddenModalSupplier supplier_id '+supplier_id);

      this.isImportSupplier=true;
 
      let params = {
        supplier_id: supplier_id
      };

      let url = "/admin/api/ProdGasSuppliers/import";
      
      axios
        .post(url, params)
        .then(response => {
            
            console.log(response.data); 
            
            if(typeof response.data !== "undefined") {
              this.isImportSupplier=false;

              if(response.data.esito==false)
                this.msg = response.data.msg_human;

              this.importFinish = true;
            }
        })
        .catch(error => {
          this.isImportSupplier=false;
          console.error("Error: " + error);
        });
      
    },      
  },
  filters: {
      html(text) {
          return text;
      },
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
@media (min-width: 576px)
.modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
}
.modal-dialog-centered {
    min-height: calc(100% - (1.75rem * 2));
}

@media (min-width: 1200px)
.modal-xl {
    max-width: 1140px;
}
@media (min-width: 992px)
.modal-lg, .modal-xl {
    max-width: 800px;
}

/* images */
.content-img-article-small {
  width: 75px;
  text-align: center;  
}
.img-article {
    max-width: 75px;
    max-height: 75px;
    display: inline;
}
.box-bio img {
    border-radius: 30px;
    float: left;
    height: 25px;
    margin-right: 35px;
    width: 25px;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

a:not[.btn] {
  color: #0a659e !important;
}
.about .content ul {
    list-style: none;
    padding: 0;
}
dl, ol, ul {
    margin-top: 0;
    margin-bottom: 1rem;
}
.about .content ul li {
    padding-bottom: 10px;
}
.about .content ul i {
    font-size: 20px;
    padding-right: 10px;
    color: #0a659e !important;
}
b, strong {
    font-weight: bolder;
}
.about .content h3 {
    font-weight: 700;
    font-size: 20px;
    color: #555555;
}
strong {
    color: #555555;
}
</style>
