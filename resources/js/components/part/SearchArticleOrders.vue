 <template>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">
      	<i class="fa fa-search" aria-hidden="true"></i>
      </span>
    </div>
    <input type="text" class="form-control" id="q-article" placeholder="Ricerca..."
          v-model="q" 
          v-on:blur="search()" />
  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
	name: "search-articles",
	data() {
		return {
		  q: null,
		};
	},
	methods: {
		search() {
			// console.log('search '+this.q);
			this.$emit('search', this.q); /* definito in <app-search-articles @search="onSearch"> */
		}		
	}
};
</script>

<style scoped>

</style>
