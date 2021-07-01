<template>

      <div class="card">

          <h5 class="card-header">{{ supplier.name }}</h5>
          <div class="text-center">
            <img v-if="supplier.img1 != ''"
            class="img-supplier-no-focus responsive"
            :src="'https://www.portalgas.it/images/organizations/contents/'+supplier.img1"
            :alt="supplier.name">
          </div>

          <div class="card-body">
            <h5 class="card-title">{{ supplier.descrizione }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">
              {{ supplier.localita }} ({{ supplier.provincia }})
            </h6>
            <p class="card-text">
             
              {{ supplier.nota }}

              <span v-if="supplier.content!=null && supplier.content.introtext!=null"
               v-html="$options.filters.html(supplier.content.introtext)"></span>

              <span>
                <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModal(supplier.id)">maggior dettaglio</a>
                
                <div v-if="isLoading" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>  
                </div> 

              </span>

            </p>
            <p class="card-text">
              <small class="text-muted">
                {{ supplier.www }}
              </small>
            </p>
          </div>
          <div class="card-footer">
          </div>

    </div> <!-- card -->

</template>


<script>
import { mapActions } from "vuex";

export default {
  name: "app-supplier",
  props: ['supplier'],
  data() {
    return {
      isLoading: false,
    };
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    clickShowModal () {
      this.showModal(true);
    }, 
    clickShowOrHiddenModal (supplier_id) {

      console.log('clickShowOrHiddenModal supplier_id '+supplier_id);

      this.isLoading=true;

      let params = {
        supplier_id: supplier_id
      };

      let url = "/api/html-suppliers/get";
      
      axios
        .post(url, params)
        .then(response => {
            /* console.log(response.data); */
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.supplier.name,
                body: response.data,
                footer: ''
              }            

              this.isLoading=false;

              this.addModalContent(modalContent);
              this.showOrHiddenModal();              
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
</style>