<template>

<div>
 

    <div v-if="isRunSuppliers" class="box-spinner"> 
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
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

    <div v-if="!isRunSuppliers && suppliers.length==0" class="alert alert-warning">
        Nessun produttore trovato
    </div>

</div> 

</template>

<script>
// @ is an alias to /src
import axios from "axios";
import { mapGetters, mapActions } from "vuex";
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
  data() {
    return {
      isRunSuppliers: false,
      suppliers: [],
      mapConfig: {
        zoom: 12,
        center: {
          lat: -6.1753871,
          lng: 106.8249641
        },
      },
      mapMarkers: [
        {
          name: 'GBK',
          lat: -6.218605,
          long: 106.802612,
        },
        {
          name: 'Ancol',
          lat: -6.1229209,
          long: 106.8228804,
        },
        {
          name: 'Monas',
          lat: -6.1753871,
          long: 106.8249641,
        }
      ],
    };
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
  components: {
  },  
  mounted() {
  },
  methods: {
    ...mapActions(["showModal", "showOrHiddenModal", "addModalContent"]),
    initializeMap() {
      const mapContainer = this.$refs.googleMap
      this.map = new this.google.maps.Map(
        mapContainer, this.mapConfig
      )
    },    
    getSuppliers() {
      this.isRunSuppliers = true;

      let url = "/api/suppliers/gets";
      let data = {
        q: this.q,
        category_id: this.category_id,
        region_id: this.region_id,
        province_id: this.province_id,
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