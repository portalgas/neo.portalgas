<template>

  <main>
 
    <modal-component-supplier></modal-component-supplier>
    <modal-component-supplier-import></modal-component-supplier-import>

    <form>

    <!-- v-if="is_logged" -->
    <suppliers-type @changeSupplierType="onChangeSupplierType" ></suppliers-type>

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
                    v-on:blur="searchByName()" />
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
            <div class="form-group">
                <select id="category_id" v-if="!isRunCategoriesSuppliers && categories!=null" class="form-control input-lg" @change="categoryOnChange($event)" v-model="category_id">
                  <option value="0">Filtra per categoria</option>
                  <option 
                      v-for="(category, index) in categories"
                      :value="category[1]">{{ category[0] }}</option>
                </select>
                <div v-if="isRunCategoriesSuppliers" class="box-spinner"> 
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>  
                </div>                 
            </div>
        </div> <!-- col-sm-12 col-xs-12 col-md-12 -->
    </div> <!-- row -->
    <div class="row">  
        <div class="col-sm-6 col-xs-6 col-md-6">
          <div class="form-group">
              <select id="region_id" v-if="!isRunRegions && regions!=null" class="form-control input-lg" @change="regionOnChange($event)" v-model="region_id">
                <option value="0">Filtra per regione</option>
                <option 
                    v-for="(region, index) in regions"
                    :value="region[1]">{{ region[0] }}</option>
              </select>
              <div v-if="isRunRegions" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                      <span class="sr-only">Loading...</span>
                  </div>  
              </div>              
          </div>
        </div> <!-- col-sm-6 col-xs-6 col-md-6 -->
        <div class="col-sm-6 col-xs-6 col-md-6">
          <div class="form-group">
              <select id="province_id" v-if="!isRunProvinces && provinces!=null" class="form-control input-lg" @change="provinceOnChange($event)" v-model="province_id">
                <option value="0">Filtra per provincia</option>
                <option 
                    v-for="(province, index) in provinces"
                    :value="index">{{ province }}</option>
              </select>
              <div v-if="isRunProvinces" class="box-spinner"> 
                  <div class="spinner-border text-info" role="status">
                      <span class="sr-only">Loading...</span>
                  </div>  
              </div>              
          </div>
        </div> <!-- col-sm-6 col-xs-6 col-md-6 -->
    </div> <!-- row -->
    </form> 


    <div class="row">
        <div class="col-sm-12 col-xs-2 col-md-3"
          v-if="suppliers.length"
          v-for="(supplier, index) in suppliers"
          :supplier="supplier"
          :key="supplier.id"
        >

              <app-supplier
                v-bind:supplier="supplier"
                v-bind:supplier_type="supplier_type">
              </app-supplier>

        </div> <!-- loop -->
    </div> <!-- row -->

    <div v-if="isRunSuppliers" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>  
    </div>

    <div v-if="!isRunSuppliers && suppliers.length==0" class="alert alert-warning">
        Nessun produttore trovato
    </div>

  </main> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import appSupplier from "../components/part/Supplier.vue";
import modalSupplier from '../components/part/ModalSupplier';
import modalSupplierImport from '../components/part/ModalSupplierImport';
import suppliersType from '../components/part/SuppliersType';

export default {
  name: "app-suppliers",
  data() {
    return {
      timer: null,
      q: '',
      category_id: 0,
      region_id: 0,
      province_id: 0,
      categories: null,
      regions: null,
      provinces: null,
      suppliers: [],
      listSuppliersFinish: false,
      isRunCategoriesSuppliers: false,
      isRunProvinces: false,
      isRunRegions: false,
      isRunSuppliers: false,
      page: 1,
      isScrollFinish: false, 
      is_logged: false,
      supplier_type: 'ALL'      
    };
  },
  components: {
    appSupplier: appSupplier,
    modalComponentSupplier: modalSupplier,
    modalComponentSupplierImport: modalSupplierImport,
    suppliersType
  },  
  mounted() {
    this.getGlobals();
    this.getCategoriesSuppliers();
    this.getRegions();
    this.getProvinces();
    this.onSearch();
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    getGlobals() {
      /*
       * variabile che arriva da cake, dichiarata come variabile in Layout/vue.ctp, in app.js settata a window. 
       * recuperata nei components con getGlobals()
       */
      this.is_logged = window.is_logged;
    },
    onChangeSupplierType: function(supplier_type) {
      this.supplier_type = supplier_type;
      console.log('supplier_type '+this.supplier_type);
      this.onSearch();
    },    
    getName:function(name) {
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
           // console.log(this.categories);
        })
        .catch(error => {
          this.isRunCategoriesSuppliers = false;
          console.error("Error: " + error);
        });    
    }, 
    getRegions() {

      this.isRunRegions = true;

      let url = "/api/regions/gets";

      axios
        .post(url)
        .then(response => {

          this.isRunRegions = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.regions = this.sortByValue(response.data.results);
           }
           // console.log(this.regions);
        })
        .catch(error => {
          this.isRunRegions = false;
          console.error("Error: " + error);
        });    
    }, 
    getProvinces() {

      this.isRunProvinces = true;

      let data = {region_id: this.region_id};

      let url = "/api/provinces/gets";

      axios
        .post(url, data)
        .then(response => {

          this.isRunProvinces = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {
             this.provinces = response.data.results;
           }
           // console.log(this.provinces);
        })
        .catch(error => {
          this.isRunProvinces = false;
          console.error("Error: " + error);
        });    
    },
    searchByName() {
      console.log('searchByName() q '+this.q);
      if(this.q=='') 
        return;

      if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
      }
      this.timer = setTimeout(() => {
          this.onSearch();
      }, 800);
    },
    categoryOnChange(event) {
        // console.log(event.target.value);
        // console.log('categoryOnChange '+this.category_id);
        this.onSearch();
    },
    regionOnChange(event) {
        // console.log(event.target.value);
        // console.log('regionOnChange '+this.region_id);
        this.onSearch();
        this.getProvinces();
    },
    provinceOnChange(event) {
        // console.log(event.target.value);
        // console.log('provinceOnChange '+this.province_id);
        this.onSearch();
    },
    onSearch: function() {
      this.suppliers = [];
      this.page = 1;
      this.listSuppliersFinish=false;
      this.scroll();
      this.isScrollFinish = false;
    },
    scroll() {

      // console.log('scroll page '+this.page+' isRunSuppliers '+this.isRunSuppliers+' isScrollFinish '+this.isScrollFinish+' listSuppliersFinish '+this.listSuppliersFinish);
      if(this.isScrollFinish || this.isRunSuppliers || this.listSuppliersFinish)
        return;

      if (this.page==1) {
         this.getSuppliers();
      }

      window.onscroll = () => {
        let scrollTop = Math.floor(document.documentElement.scrollTop);
        let bottomOfWindow = scrollTop + window.innerHeight > (document.documentElement.offsetHeight - 10);
        // console.log((scrollTop + window.innerHeight)+' '+(document.documentElement.offsetHeight - 10));

        /*
        scrollTop    height to top
        innerHeight  height windows
        offsetHeight height page
        console.log('document.documentElement.scrollTop '+scrollTop);
        console.log('window.innerHeight '+window.innerHeight);
        console.log('document.documentElement.offsetHeight '+document.documentElement.offsetHeight);
        console.log('bottomOfWindow '+bottomOfWindow);
        */

        if (bottomOfWindow && !this.isRunSuppliers && !this.isScrollFinish && !this.listSuppliersFinish) {
            this.getSuppliers();
        }
      };  
    },    
    getSuppliers() {

      this.isRunSuppliers = true;

      let url = '';
      switch(this.supplier_type) {
        case 'ALL':
          url = "/api/suppliers/gets";
        break;
        case 'OWNER-ARTICLES': // produttori che gestiscono il listino articoli 
          url = "/api/suppliers/produttoriGets";
        break;
        default:
          url = "/api/suppliers/gets";
      }

      let data = {
        q: this.q,
        category_id: this.category_id,
        region_id: this.region_id,
        province_id: this.province_id,
        page: this.page
      }
      console.log(url, data);

      axios
        .post(url, data)
        .then(response => {

          this.isRunSuppliers = false;

           // console.log(response.data);
           if(typeof response.data !== "undefined") {

              if(response.data.results.length==0) {
                /*
                 * non trovato altri elementi
                 */
                 this.listSuppliersFinish = true;
              }
              else {
                this.listSuppliersFinish = false;
                
                var data = response.data.results;
                for (var i = 0; i < data.length; i++) {
                    this.suppliers.push(data[i]);
                } 

            //   this.suppliers = response.data.results;

                this.page++;
                this.isScrollFinish = false;
              }
           }
           console.log(this.suppliers);
        })
        .catch(error => {
          this.isRunSuppliers = false;
          this.isScrollFinish = true;
          console.error("Error: " + error);
        });    
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