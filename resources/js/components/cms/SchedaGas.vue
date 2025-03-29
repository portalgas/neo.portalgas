<template>
    <main>
        <p>
            <img :src="'https://www.portalgas.it/images/organizations/contents/'+organization.img1" />
        </p>
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
        <p>
            {{ organization.indirizzo }} {{ organization.localita }} ({{ organization.provincia }}) {{ organization.cap }}
        </p>
        <p>
            <a :href="'https://www.portalgas.it/contattaci?contactOrganizationId='+organization.id" title="scrivi una mail al G.A.S." class="btn btn-primary btn-block">Contattaci scrivendo una mail</a>
        </p>
    </main>
</template>

<script>
import GoogleMap from "../common/GoogleMap.vue";

export default {
    name: "app-cms-scheda",
    components: {
        GoogleMap
    },
    data() {
        return {
            apikey: this.appConfig.$googlemap_api_key,
        };
    },
    props: {
        organization: {}
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
            let lat = '';
            if(this.organization.lat!='')
                lat = parseFloat(this.organization.lat);

            let lng = '';
            if(this.organization.lng!='')
                lng = parseFloat(this.organization.lng);

            return [
                {
                    name: this.organization.name,
                    lat: lat,
                    lng: lng
                }
            ]
        }
    },
};
</script>


