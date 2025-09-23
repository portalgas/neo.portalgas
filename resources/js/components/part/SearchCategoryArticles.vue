 <template>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">
      	<i class="fa fa-tags" aria-hidden="true"></i>
      </span>
    </div>

    <div v-if="is_run" class="box-spinner">
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <select
          v-if="!is_run"
          name="search_categories_article_id"
          id="search-categories-article_id"
          class="form-control"
          :required="true"
          v-on:change="search()"
          v-model="search_categories_article_id" >
          <option value="">Filtra per categoria</option>
          <option v-for="(categories_article) in search_categories_articles" :value="categories_article.id" v-html="$options.filters.html(categories_article.name)"></option>
        </select>

  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
	name: "search-category-articles",
	data() {
		return {
      is_run: false,
      search_categories_article_id: 0,
      search_categories_articles: [],
		  order_type_id: 0,
		  order_id: 0,
		};
	},
	methods: {
    gets() {

      this.is_run = true;

      this.order_type_id = this.$route.params.order_type_id;
      this.order_id = this.$route.params.order_id;

      let url = "/admin/api/categories-articles/gets";
      let params = {
        search_order_id: this.order_id,
        search_order_type_id: this.order_type_id,
      };
      /* console.log(params, '/admin/api/categories-articles/gets'); */

      axios
        .post(url, params)
        .then(response => {

          this.is_run = false;

          /* console.log(response.data); */
          if(typeof response.data !== "undefined") {
            this.search_categories_articles = response.data.results;
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
			// console.log('search '+this.search_categories_article_id);

      if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
      }
      this.timer = setTimeout(() => {
					this.$emit('searchCategoryArticles', this.search_categories_article_id); /* definito in <app-search-articles @search="onSearch"> */
      }, 800);
		}
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
