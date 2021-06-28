<template>

<div>
 
    <form>
    <div class="row">
      <div class="col-sm-12 col-xs-12 col-md-12"> 

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </span>
              </div>
              <input type="text" class="form-control input-lg" id="q" autocomplete="off"  placeholder="Ricerca..."
                    v-model="q" 
                    v-on:blur="search()" />
            </div>
            <!-- div class="panel-footer" v-if="suppliers.length">
              <ul class="list-group">
                <a href="#" class="list-group-item" v-for="supplier in suppliers" @click="getName(supplier.id)">{{ supplier.name }}</a>
              </ul>
            </div-->
      </div>
    </div> <!-- row -->  
    <div class="row">  
        <div class="col-sm-12 col-xs-12 col-md-12">
              <select id="category_id" v-if="!isRunCategoriesSuppliers && categories!=null" class="form-control input-lg" @change="categoryOnChange($event)" v-model="category_id">
                <option 
                    v-for="(category, index) in categories"
                    :value="category[1]">{{ category[0] }}</option>
              </select>
      </div> <!-- col-sm-12 col-xs-12 col-md-12 -->
    </div> <!-- row -->
    </form> 


    <div v-if="isRunSuppliers" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>  
    </div>


    <div class="row">
      <div class="col-sm-12 col-xs-2 col-md-3"
          v-if="!isRunSuppliers && suppliers.length"
          v-for="(supplier, index) in suppliers"
          :supplier="supplier.id"
          :key="supplier.id"
          >

      <div class="card">
          <h5 class="card-header">{{ supplier.name }}</h5>
          <img v-if="supplier.img1 != ''"
            class="card-img-top"
            :src="'https://www.portalgas.it/images/organizations/contents/'+supplier.img1"
            :alt="supplier.name">

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
                <a class="btn btn-primary btn-block btn-sm cursor-pointer" @click="clickShowOrHiddenModal(supplier.supplier.id)">maggior dettaglio</a>
                
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
            <small class="text-muted">
              <a class="btn btn-primary" v-on:click="selectMarket(supplier)">Acquista</a>
            </small>
          </div>

          </div> <!-- card -->
        </div> <!-- loop -->
      </div> <!-- row -->

    <div v-if="!isRunSuppliers && suppliers.length==0" class="alert alert-warning">
        Nessun produttore trovato
    </div>

</div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "app-suppliers",
  data() {
    return {
      timer: null,
      q: '',
      category_id: 0,
      categories: null,
      suppliers: [],
      isRunCategoriesSuppliers: false
      isRunSuppliers: false,
      isLoading: false
    };
  },
  mounted() {
    this.getCategoriesSuppliers();
    this.search();
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    getName:function(name){
      this.q = name;
      this.suppliers = [];
    },    
    getCategoriesSuppliers() {

      this.isRunCategoriesSuppliers = true;

      let url = "/api/categories-suppliers/gets";

      axios
        .post(url)
        .then(response => {

          this.isRunCategoriesSuppliers = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.categories = this.sortByValue(response.data.results);
           }
           console.log(this.categories);
        })
        .catch(error => {
          this.isRunCategoriesSuppliers = false;
          console.error("Error: " + error);
        });    
    },
    search() {
      console.log('search() q '+this.q);
      if(this.q=='') 
        return;

      if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.gets();
      }, 800);
    },
    categoryOnChange(event) {
        console.log(event.target.value);
        console.log('categoryOnChange '+this.category_id);
        this.gets();
    },
    gets() {
      this.isRunSuppliers = true;

      let url = "/api/suppliers/gets";
      let data = {
        q: this.q,
        category_id: this.category_id 
      }
      console.log(url, data);

      axios
        .post(url, data)
        .then(response => {

          this.isRunSuppliers = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.suppliers = response.data.results;
           }
           console.log(this.suppliers);
        })
        .catch(error => {
          this.isRunSuppliers = false;
          console.error("Error: " + error);
        });    
    },
    get() {

      this.isRunSuppliers = true;

      let url = "/api/suppliers/get";

      axios
        .post(url)
        .then(response => {

          this.isRunSuppliers = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.suppliers = response.data.results;
           }
           console.log(this.suppliers);
        })
        .catch(error => {
          this.isRunSuppliers = false;
          console.error("Error: " + error);
        });    
    },    
    clickShowOrHiddenModal (supplier_id) {

      console.log('clickShowOrHiddenModal supplier_id '+supplier_id);

      this.isLoading=true;

      let params = {
        supplier_id: supplier_id
      };

      let url = "/admin/api/html-article-orders/get";
      axios
        .post(url, params)
        .then(response => {
            // console.log(response.data);
            if(typeof response.data !== "undefined") {

              var modalContent = {
                title: this.article.name,
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
          this.isRunDeliveries=false;
          console.error("Error: " + error);
        });
    }, 
    selectMarket(supplier) {
        /* console.log('selectMarket'); */
        /* console.log(supplier); */
      
        this.$router.push({ name: 'SocialShop', params: {supplier_id: supplier.id}})
    },
    sortByValue(jsObj) {
        var sortedArray = [];
        for(var i in jsObj) {
            sortedArray.push([jsObj[i], i]);
            // sortedArray.push([i, jsObj[i]]); sortByKey
        }
        return sortedArray.sort();
    }            
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
};
</script>

<style scoped>
</style>