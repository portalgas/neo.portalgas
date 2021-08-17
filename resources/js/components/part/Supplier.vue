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

              <span>
                <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModalSupplier(supplier.id)">maggior dettaglio</a>
                
                <div v-if="isLoading" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 

              </span>

              <div v-if="supplier.voto!=0" v-html="supplier.voto_html"></div>

            </div> <!-- card-text -->
            
          </div> <!-- cart-body -->

          <div class="card-footer">
              <ul class="contact">
                <li v-if="supplier.slug!=''">
                    <a :href="'/site/produttore/'+supplier.slug" title="link diretto"><i class="fas fa-link"></i></a>
                </li>
                <li v-if="supplier.www!=''">
                    <a :href="supplier.www" target="_blank" title="vai al sito del produttore"><i class="fas fa-globe"></i></a>
                </li>
                <li v-if="supplier.mail!=''">
                    <a :href="'mailto:'+supplier.mail" title="scrivigli una mail"><i class="fas fa-envelope"></i></a>
                </li>
                <li v-if="supplier.telefono!=''">
                    <a :href="'tel:'+supplier.telefono" title="telefona al produttore"><i class="fas fa-phone"></i></a>
                </li>
            </ul>
          </div>

    </div> <!-- card -->

</template>


<script>
import { mapActions } from "vuex";
import modalSupplier from '../../components/part/ModalSupplier';

export default {
  name: "app-supplier",
  props: ['supplier'],
  data() {
    return {
      isLoading: false,
    };
  },
  components: {
    modalSupplier: modalSupplier
  },  
  methods: {
    ...mapActions(["showModalSupplier", "showOrHiddenModalSupplier", "addModalContent"]),
    clickShowModalSupplier () {
      this.showModalSupplier(true);
    }, 
    clickShowOrHiddenModalSupplier (supplier_id) {

      console.log('clickShowOrHiddenModalSupplier supplier_id '+supplier_id);

      this.isLoading=true;

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

              this.isLoading=false;

              this.addModalContent(modalContent);
              this.showOrHiddenModalSupplier();              
            }
        })
        .catch(error => {
          this.isLoading=false;
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
</style>