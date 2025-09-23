 <template>

  <div class="input-group mb-3">

    <div v-if="is_run" class="box-spinner">
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

      <div class="form-check-inline"
           v-if="!is_run"
           v-for="(article_type) in search_article_types" :key="'article_type-'+article_type.id">
          <label class="form-check-label">
              <input type="checkbox" class="form-check-input" :value="article_type.id"
                     v-on:change="search()"
                     v-model="search_article_types_ids"> {{ article_type.name }}
          </label>
      </div>

  </div>

</template>

<script>
export default {
	name: "search-article-types",
	data() {
		return {
            is_run: false,
            search_article_types_ids: [],
            search_article_types: []
        };
	},
	methods: {
    gets() {

      this.is_run = true;

      let url = "/admin/api/article-types/gets";
      axios
        .get(url)
        .then(response => {

          this.is_run = false;

          /* console.log(response.data); */
          if(typeof response.data !== "undefined") {
            this.search_article_types = response.data.results;
          }
          else {
            console.error("Error: " + response.message);
            this.is_run = false;
          }
        })
        .catch(error => {
          this.is_run = false;
          console.error("Error: " + error);
        });
    },
	search() {
		this.$emit('searchArticleTypes', this.search_article_types_ids); /* definito in <app-search-article-types @search="onSearch"> */
    },
},
mounted() {
    /* console.log('mounted searchCategoryArticles'); */
    this.gets();
  },
  filters: {
    html(text) {
      return text;
    },
  }
}
</script>

<style scoped>

</style>
