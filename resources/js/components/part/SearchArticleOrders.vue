 <template>

  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text">
      	<i class="fa fa-search" aria-hidden="true"></i>
      </span>
    </div>
    <input type="text" class="form-control" id="q-article" placeholder="Ricerca..."
          v-model="q" 
          v-on:keyup="search()" />
  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
	name: "search-articles-orders",
	data() {
		return {
		  q: null,
		};
	},
	methods: {
		search() {
			// console.log('search '+this.q);

      if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
      }
      this.timer = setTimeout(() => {
					this.$emit('search', this.q); /* definito in <app-search-articles @search="onSearch"> */
      }, 800);		
		}		
	}
};
</script>

<style scoped>

</style>
