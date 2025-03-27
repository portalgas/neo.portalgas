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
                        <li>
                            <a :href="'/gas/'+slugGas+'/home'" class="btn btn-primary btn-block text-left">Home del GAS</a>
                        </li>
                        <li v-for="menu in menus" :key="menu.id">
                            <a :href="'/gas/'+slugGas+'/'+menu.slug" class="btn btn-primary btn-block text-left"
                               v-if="menu.cms_menu_type.code=='PAGE'">{{ menu.name }}</a>

                            <a :href="'/gas/'+slugGas+'/'+menu.cms_docs[0].id" class="btn btn-primary btn-block text-left"
                               target="_blank"
                               v-if="menu.cms_menu_type.code=='DOC'">
                                <svg fill="white" width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 144-208 0c-35.3 0-64 28.7-64 64l0 144-48 0c-35.3 0-64-28.7-64-64L0 64zm384 64l-128 0L256 0 384 128zM176 352l32 0c30.9 0 56 25.1 56 56s-25.1 56-56 56l-16 0 0 32c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-48 0-80c0-8.8 7.2-16 16-16zm32 80c13.3 0 24-10.7 24-24s-10.7-24-24-24l-16 0 0 48 16 0zm96-80l32 0c26.5 0 48 21.5 48 48l0 64c0 26.5-21.5 48-48 48l-32 0c-8.8 0-16-7.2-16-16l0-128c0-8.8 7.2-16 16-16zm32 128c8.8 0 16-7.2 16-16l0-64c0-8.8-7.2-16-16-16l-16 0 0 96 16 0zm80-112c0-8.8 7.2-16 16-16l48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 32 32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0 0 48c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-64 0-64z"/></svg>
                                {{ menu.cms_docs[0].name }}</a>

                            <a :href="menu.options" class="btn btn-primary btn-block text-left"
                               target="_blank"
                               v-if="menu.cms_menu_type.code=='LINK_EXT'">
                                <svg fill="white" width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"/></svg>
                                {{ menu.name }}</a>
                        </li>
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
