<template>

      <div class="card">

          <h5 class="card-header">{{ supplier.name }}</h5>
          <div class="text-center">
            <img v-if="supplier.img1 != ''"
            class="img-supplier-no-focus responsive"
            :src="supplier.img1"
            :alt="supplier.name">
          </div>

          <div class="card-body">

            <span v-if="supplier.categories_supplier!=null">Categoria: {{ supplier.categories_supplier.name }}</span>

            <h5 class="card-title">{{ supplier.descrizione }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">
              {{ supplier.localita }} <span v-if="supplier.provincia">({{ supplier.provincia }})</span> {{ supplier.cap }} 
            </h6>
            <div class="card-text">
             
              {{ supplier.nota }}

              <div class="box-more-info">
                <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModalSupplier(supplier.id)">maggior dettaglio</a>
                
                <div v-if="isLoadingSupplier" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 
              </div>

              <div v-if="supplier.hasOrganization!=null && supplier.hasOrganization">
                <a class="btn btn-primary btn-block btn-sm no-cursor-pointer">rifornisce già il tuo G.A.S.</a> 
              </div>

              <div v-if="supplier.hasOrganization!=null && !supplier.hasOrganization && supplier_type=='OWNER-ARTICLES'">
                <a class="btn btn-success btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModalSupplierImport(supplier.id)">con il mio G.A.S., voglio comprare dal produttore</a>
                
                <div v-if="isLoadingSupplierImport" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 
              </div>
                 
              <div v-if="supplier.voto!=0" v-html="supplier.voto_html"></div>

            </div> <!-- card-text -->
            
          </div> <!-- cart-body -->

          <div class="card-footer">
              <ul class="contact">
                <li v-if="supplier.slug!='' && supplier.slug!=null">
                    <a :href="'/site/produttore/'+supplier.slug" title="link diretto"><i class="fas fa-link"></i></a>
                </li>
                <li v-if="supplier.www!='' && supplier.www!=null">
                    <a :href="supplier.www" target="_blank" title="vai al sito del produttore"><i class="fas fa-globe"></i></a>
                </li>
                <li v-if="supplier.mail!='' && supplier.mail!=null">
                    <a :href="'mailto:'+supplier.mail" title="scrivigli una mail"><i class="fas fa-envelope"></i></a>
                </li>
                <li v-if="supplier.telefono!='' && supplier.telefono!=null">
                    <a :href="'tel:'+supplier.telefono" title="telefona al produttore"><i class="fas fa-phone"></i></a>
                </li>
            </ul>
          </div>

    </div> <!-- card -->

</template>


<script>
import { mapActions } from "vuex";
import modalSupplier from '../../components/part/ModalSupplier';
import modalSupplierImport from '../../components/part/ModalSupplierImport';

export default {
  name: "app-supplier",
  props: ['supplier', 'supplier_type'],
  data() {
    return {
      isLoadingSupplier: false,
      isLoadingSupplierImport: false,
      is_logged: false
    };
  },
  components: {
    modalSupplier: modalSupplier,
    modalSupplierImport: modalSupplierImport
  },  
  methods: {
    ...mapActions(["showModalSupplier", "showOrHiddenModalSupplier", "showModalSupplierImport", "showOrHiddenModalSupplierImport", "addModalContent"]),
    clickShowModalSupplier () {
      this.showModalSupplier(true);
    }, 
    clickShowOrHiddenModalSupplier (supplier_id) {

      /* console.log('clickShowOrHiddenModalSupplier supplier_id '+supplier_id); */

      this.isLoadingSupplier=true;

      let params = {
        supplier_id: supplier_id
      };

      // let url = "/api/html-suppliers/get";
      let url = "/api/suppliers/get";
      
      axios
        .post(url, params)
        .then(response => {
            
            /* console.log(response.data); */
            
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.supplier.name,
                body: '',
                entity: response.data.results,
                footer: ''
              }            

              this.isLoadingSupplier=false;

              this.addModalContent(modalContent);
              this.showOrHiddenModalSupplier();              
            }
        })
        .catch(error => {
          this.isLoadingSupplier=false;
          console.error("Error: " + error);
        });
    },   
    clickShowModalSupplierImport () {
      this.showModalSupplierImport(true);
    }, 
    clickShowOrHiddenModalSupplierImport (supplier_id) {

      /* console.log('clickShowOrHiddenModalSupplierImport supplier_id '+supplier_id); */

      this.isLoadingSupplierImport=true;

      let params = {
        supplier_id: supplier_id
      };

      // let url = "/api/html-suppliers/get";
      let url = "/api/suppliers/get";
      
      axios
        .post(url, params)
        .then(response => {
            
            console.log(response.data); 
            
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.supplier.name,
                body: '',
                entity: response.data.results,
                footer: ''
              }            

              this.isLoadingSupplierImport=false;

              this.addModalContent(modalContent);
              this.showOrHiddenModalSupplierImport();              
            }
        })
        .catch(error => {
          this.isLoadingSupplierImport=false;
          console.error("Error: " + error);
        });
    },   

  },
};
</script>

<style scoped>
@media screen and (min-width: 600px) {
  .card {
    min-height: 400px;
  }
}
.card {
    margin-bottom: 5px;
}
.img-supplier-no-focus {
  padding-top: 5px;
}
ul.contact {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
ul.contact li {
  float: left;
  padding-right: 20px;
}
ul.contact li a {
  color: #0a659e !important;
  font-size: 25px;
}
.box-more-info {
  padding-bottom: 10px;
}
</style>