<template>

    <main>

       <div v-if="isLoading" class="box-spinner">
          <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
          </div>
       </div>
       <section v-if="!isLoading && organization">

           <div class="row">
                <div class="col-md-12">
                    <h2>
                        <span style="float:left;">{{ organization.name }}</span>
                        <span style="float:right;">
                        <Organizations :slug="slugGas" />
                    </span>
                    </h2>
                </div>
           </div>

            <div class="row">
                <div class="col col-2">
                    <ul class="menu">
                        <li v-for="menu in menus" :key="menu.id">
                            <a :href="'/gas/'+slugGas+'/'+menu.slug" class="btn btn-primary btn-block text-left">{{ menu.label }}</a></li>
                    </ul>
                    <pre>{{  }}</pre>
                </div>
                <div class="col col-7">
                    <div v-html="$options.filters.html(content)"></div>
                </div>
                <div class="col col-3 text-center">
                    <p><img :src="'https://www.portalgas.it/images/organizations/contents/'+organization.img1" /></p>
                    <p>
                        {{ organization.indirizzo }} {{ organization.localita }} ({{ organization.provincia }}) {{ organization.cap }}
                    </p>
                </div>
            </div>

       </section>

  </main>

</template>

<script>
import axios from "axios";
import Organizations from "../components/common/Organizations.vue";

export default {
  name: "app-gas",
  components: {
      Organizations
  },
  data() {
    return {
        slugGas: null,
        slugPage: null,
        apikey: this.appConfig.$googlemap_api_key,
        isLoading: false,
        is_logged: false,
        organization: null,
        content: null,
        menus: null,
    };
  },
  mounted() {
    console.log('mounted gas');
      console.log('slugGas '+this.$route.params.slugGas);
      this.slugGas = this.$route.params.slugGas;
      if(this.slugGas=='')
          return;

      console.log('slugPage '+this.$route.params.slugPage);
      this.slugPage = this.$route.params.slugPage;
      if(this.slugPage=='')
          this.slugPage=='home';

      this.getMenu();
      this.getPage();
  },
    watch: {
        '$route'(to, from) {
            this.getMenu();
            this.getPage();
        },
    },
  methods: {
      getMenu:function() {
          if(this.slugGas=='')
              this.slugGas=='home';
          let url = '/api/gas/menu/'+this.slugGas;
          axios
              .get(url)
              .then(response => {
                  this.isLoading = false;
                  // console.log(response.data);
                  if(typeof response.data !== "undefined") {
                      this.menus = response.data.results;
                  }
                  this.isLoading = false;
              })
              .catch(error => {
                  this.isLoading=false;
                  console.error("Error: " + error);
              });
      },
      getPage:function() {
          console.log('slugGas '+this.$route.params.slugGas);
          this.slugGas = this.$route.params.slugGas;
          if(this.slugGas=='')
              return;

          console.log('slugPage '+this.$route.params.slugPage);
          this.slugPage = this.$route.params.slugPage;
          if(this.slugPage=='')
              this.slugPage=='home';


          this.isLoading = true;

          let url = "/api/gas/page/"+this.slugGas+'/'+this.slugPage;
          axios
              .get(url)
              .then(response => {
                  console.log(response.data);
                  if(typeof response.data !== "undefined") {
                      let results = response.data.results;
                      this.content = results.content;
                      this.organization = results.organization;
                  }
                  this.isLoading=false;
              })
              .catch(error => {
                  this.isLoading=false;
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
h2 {
    height: 65px;
}
.menu {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.menu li {
    padding-bottom: 2px;
}
</style>
