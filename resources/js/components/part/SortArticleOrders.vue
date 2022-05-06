 <template>

 	<div class="btn-toolbar d-flex flex-row-reverse" role="toolbar" aria-label="sort">

    <div class="input-group mb-3" style="width:100%">
      <div class="input-group-prepend">
        <label class="input-group-text" for="sort">
          <i class="fas fa-sort"></i>
        </label>
      </div>
        <select class="form-control" id="sort" @change="onChange" v-model="sort">
          <option value="codice-asc" selected>Codice in ordine crescente</option>
          <option value="codice-desc">Codice in ordine decrescente</option>
          <option value="name-asc">Nome in ordine crescente</option>
          <option value="name-desc">Nome in ordine decrescente</option>
          <option value="prezzo-asc">Prezzo in ordine crescente</option>
          <option value="prezzo-desc">Prezzo in ordine decrescente</option>
        </select>
    </div>
	</div>

</template>

<script>
export default {
	name: "sort-articles",
	data() {
		return {
		  sort: 'codice-asc'
		};
	},	
	methods: {
		onChange() {
			// console.log('sort '+this.sort);
			
			/* definito in <app-sort-articles @changeSort="onChangeSort"> */
			this.$emit('changeSort', this.sort); 
		},
	
	}
};
</script>

<style scoped>
</style>
