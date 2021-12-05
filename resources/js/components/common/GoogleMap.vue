<template>
    <div>

        <div id="map" ref="map">
          <template v-if="!!this.google">
              <slot/>
          </template>
        </div>

    </div>

</template>

<script>
import { Loader } from '@googlemaps/js-api-loader';

export default {
  props: {
    config: Object,
    apikey: String,
    markers: Array
  },
  data() {
    return {
      google: null,
      map: null,
      ms: []
    };
  },
  computed: {
    svgMarker() {
      return {
          path: "M10.453 14.016l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM12 2.016q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
          fillColor: "orange",
          fillOpacity: 0.9,
          strokeWeight: 0,
          rotation: 0,
          scale: 2,
          anchor: new google.maps.Point(15, 30),
        }
    },
  },  
  watch: { 
    markers: function(newVal, oldVal) { 

        var _this = this;

           console.log('Prop changed: ', newVal, ' | was: ', oldVal);

            /*
             * elimino markers precedenti
             */
            for (let i = 0; i < this.ms.length; i++) {
                _this.ms[i].setMap(null);
            }
            _this.ms = [];

            /*
             * creo i nuovi markers
             */            
            newVal.forEach(marker => {
                  _this.ms.push(new google.maps.Marker({
                      title: marker.name,
                      position: new google.maps.LatLng(marker.lat, marker.lng),
                      icon: _this.svgMarker,
                      map: _this.map
                 }));

                 /* 
                  * centra la mappa
                  */
                 _this.map.panTo(new google.maps.LatLng(marker.lat, marker.lng));
             });      
    }
  },
  async mounted() { 

     var _this = this;

     const config = JSON.parse(JSON.stringify(this.config));
     // console.log('mounted config JSON', config);
     // console.log('mounted markers ', this.markers);
     
     const mapContainer = document.getElementById("map");

     const googleMapApi = new Loader({
        apiKey: this.apikey,
        version: "weekly",
        libraries: ["places"]
     });

     this.google = googleMapApi.load()
        .then((google) => {
            // console.log(this.config);
            _this.map = new google.maps.Map(mapContainer, this.config);

            this.markers.forEach(marker => {
                 _this.ms.push(new google.maps.Marker({
                      title: marker.name,
                      position: new google.maps.LatLng(marker.lat, marker.lng),
                      icon: _this.svgMarker,
                      map: _this.map
                 }));

                 // _this.ms.setMap(_this.map); 
             });

             return _google;
        })
        .catch(e => {
          // do something
        });
  },
  created() {
  },  
  methods: {
  }
}
</script>

<style>
#map {
  height: 50vh;
  width: 100%;
}
</style>