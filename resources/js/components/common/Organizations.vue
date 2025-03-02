<template>
    <main>
        <div v-if="isLoading" class="box-spinner">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <select
            v-if="!isLoading"
            name="organization_slug"
            id="organization_slug"
            class="form-control"
            :required="true"
            v-on:change="selectOrganizationId()"
            v-model="organization_slug" >
            <option value="">Filtra per GAS</option>
            <option v-for="(organization, slug) in organizations" :value="slug" v-html="$options.filters.html(organization)"></option>
        </select>
    </main>
</template>

<script>
import { mapGetters } from "vuex";
import axios from "axios";

export default {
    name: "app-organizations",
    props: {
        slug: null
    },
    data() {
        return {
            isLoading: false,
            organization_slug: null,
            organizations: null
        };
    },
    mounted() {
        /* console.log('mounted organizations');*/
        this.gets();
        this.organization_slug = this.slug
    },
    methods: {
        gets:function() {
            let params = {
                type: 'GAS'
            }
            this.isLoading = true;
            let url = '/api/organizations/gets';
            axios
                .post(url, params)
                .then(response => {
                    this.isLoading = false;
                    // console.log(response.data);
                    if(typeof response.data.results!== "undefined") {
                        this.organizations = response.data.results;
                    }
                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading=false;
                    console.error("Error: " + error);
                });
        },
        selectOrganizationId:function() {
            this.$router.push('/gas/'+this.organization_slug+'/home');
        },
    },
    filters: {
        html(text) {
            return text;
        }
    }
};
</script>

<style scoped>
</style>
