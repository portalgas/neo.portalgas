<template>

    <main>

      <div v-if="isLoading" class="box-spinner">
          <div class="spinner-border text-info" role="status">
              <span class="sr-only">Loading...</span>
          </div>
      </div>
      <div v-else>
          <section v-if="organization!=null">
              <div class="row">
                  <div class="col-md-12">
                      <h2>
                          <span style="float:left;">Produttori</span>
                          <span style="float:right;">
                              <Organizations :slug="slugGas" :organization="organization" />
                          </span>
                      </h2>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-2">
                      <Menu :slugGas="slugGas"></Menu>
                  </div>
                  <div class="col-md-10">

                    <div v-if="isRunSuppliers" class="box-spinner"> 
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>  
                    </div>
                    <div v-else>

                      <p>
                        <google-map
                            v-if="organization.lat!='' && organization.lng!=''"
                            :config="mapConfig"
                            :apikey="apikey"
                            :markers="mapMarkers"
                        >
                            <!-- GoogleMapMarkers :markers="mapMarkers"/ -->
                        </google-map>
                      </p>

                      <div class="container-item">
                        <div class="item" v-for="(suppliers_organization, index)  in suppliers_organizations">
                          <img v-if="suppliers_organization.supplier.img1!=''" width="50px" 
                               :src="'https://www.portalgas.it/images/organizations/contents/'+suppliers_organization.supplier.img1" /> {{ suppliers_organization.name }}
                        </div>
                      </div>
                    </div>



                    <!-- google-map
                      :config="mapConfig">
                      <GoogleMapMarkers :markers="mapMarkers"/>
                    </google-map -->

                    <div id="map" style="width:500px;height:500px;"></div>

                    <!-- div class="google-map" ref="googleMap"></div>
                    <template v-if="Boolean(this.google) && Boolean(this.map)">
                      <slot
                        :google="google"
                        :map="map"
                      />
                    </template -->

                    <div v-if="!isRunSuppliers && suppliers_organizations.length==0" class="alert alert-warning">
                        Nessun produttore trovato
                    </div>                    

                  </div>
              </div>
          </section>
      </div>

    </main>

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
import Organizations from "../components/common/Organizations.vue";
import Menu from "../components/cms/Menu.vue";
import GoogleMap from "../components/common/GoogleMap.vue";

// import { Loader } from '@googlemaps/js-api-loader';
// import GoogleMap from '../common/GoogleMap';
// import GoogleMapMarkers from '../common/GoogleMapMarkers';
/*
const loader = new Loader({
  apiKey: "",
  version: "weekly",
  libraries: ["places"]
});

const mapOptions = {
  center: {
    lat: 43.8797,
    lng: 7.90586
  },
  zoom: 4
}

loader
  .load()
  .then((google) => {
    new google.maps.Map(document.getElementById("map"), mapOptions);
  })
  .catch(e => {
    console.error(e);
  });
*/

export default {
  name: "app-gas-suppliers",
  components: {
      Organizations,
      Menu,
      GoogleMap
  },  
  data() {
    return {
      isLoading: false,
      organization: null,
      slugGas: null,
      isRunSuppliers: false,
      suppliers_organizations: [],
      apikey: this.appConfig.$googlemap_api_key
    };
  },  
  mounted() {
    // console.log('mounted gas');
    // console.log('slugGas '+this.$route.params.slugGas);
    this.slugGas = this.$route.params.slugGas;
    if(this.slugGas=='')
        return;

    this.getOrganization();
    },  
    methods: {
        getOrganization:function() {
            this.isLoading = true;

            let url = "/api/gas/organization/"+this.slugGas;
            axios
                .get(url)
                .then(response => {
                    // console.log(response.data, 'getOrganization');
                    if(typeof response.data !== "undefined") {
                        this.organization = response.data.results;
                    }
                    this.isLoading=false;

                    this.getSuppliers();
                })
                .catch(error => {
                    this.isLoading=false;
                    console.error("Error: " + error, 'getOrganization');
                });
        },
        ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
        initializeMap() {
          const mapContainer = this.$refs.googleMap
          this.map = new this.google.maps.Map(
            mapContainer, this.mapConfig
          )
        },    
        getSuppliers() {
          this.isRunSuppliers = true;

          let url = "/api/gas/suppliers/"+this.slugGas;
          axios
            .get(url)
            .then(response => {

              this.isRunSuppliers = false;

              // console.log(response.data);
              if(typeof response.data !== "undefined") {
                this.suppliers_organizations = response.data.results;
              }
              // console.log(this.suppliers_organizations);
            })
            .catch(error => {
              this.isRunSuppliers = false;
              console.error("Error: " + error);
            });    
        }                 
    }, 
    computed: {
        mapConfig() {
            return {
                zoom: 10,
                center: {
                    lat: parseFloat(this.organization.lat),
                    lng: parseFloat(this.organization.lng)
                }
            }
        },
        mapMarkers() {
            return this.suppliers_organizations.map(suppliers_organization => ({
              marker: suppliers_organization.name,
              lat: parseFloat(suppliers_organization.supplier.lat),
              lng: parseFloat(suppliers_organization.supplier.lng)
            })); 
        }
    },       
  /*
  data() {
    return {
      google: null,
      map: null
    }
  }, 
  components: {
    GoogleMap,
    GoogleMapMarkers,
  },   
  async mounted() {
   
    const googleMapApi = await GoogleMapsApiLoader({
      apiKey: this.appConfig.$googlemap_api_key
    })
    console.log(googleMapApi);

    const googleMapApi = await new Loader({
      apiKey: this.appConfig.$googlemap_api_key,
      version: "weekly",
      libraries: ["places"]
    });

    this.google = googleMapApi
    this.initializeMap()
  }, 
  */
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
h2 {
    height: 65px;
}
.container-item {
  display: flex;       /* Attiva il layout flex */
  flex-wrap: wrap;    /* Permette agli elementi di andare a capo */
  gap: 10px;          /* Crea spazio tra i div (senza usare i margini) */
}

.item {
  width: 200px;       /* Ogni div avrà una larghezza base */
  height: 100px;
}
</style>
