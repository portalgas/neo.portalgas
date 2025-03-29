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
                            <span style="float:left;">{{ organization.name }}</span>
                            <span style="float:right;">
                        <Organizations :slug="slugGas" :organization="organization" />
                    </span>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-2">
                        <Menu :slugGas="slugGas"></Menu>
                    </div>
                    <div class="col col-7">
                        <Content :slugGas="slugGas" :slugPage="slugPage"></Content>
                    </div>
                    <div class="col col-3 text-center">
                        <SchedaGas :organization="organization"></SchedaGas>
                    </div>
                </div>
            </section>
        </div>

  </main>

</template>

<script>
import Organizations from "../components/common/Organizations.vue";
import Menu from "../components/cms/Menu.vue";
import Content from "../components/cms/Content.vue";
import SchedaGas from "../components/cms/SchedaGas.vue";
import axios from "axios";

export default {
  name: "app-gas",
  components: {
      Organizations,
      Menu,
      SchedaGas,
      Content
  },
  data() {
    return {
        isLoading: false,
        slugGas: null,
        slugPage: null,
        organization: null
    };
  },
  mounted() {
    // console.log('mounted gas');
    // console.log('slugGas '+this.$route.params.slugGas);
    this.slugGas = this.$route.params.slugGas;
    if(this.slugGas=='')
        return;

    // console.log('slugPage '+this.$route.params.slugPage);
    this.slugPage = this.$route.params.slugPage;
    if(this.slugPage=='')
        this.slugPage=='home';

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
                })
                .catch(error => {
                    this.isLoading=false;
                    console.error("Error: " + error, 'getOrganization');
                });
        },
    },
};
</script>

<style scoped>
h2 {
    height: 65px;
}
</style>
