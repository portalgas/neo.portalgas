 <template>

	<div class="btn-toolbar d-flex flex-row-reverse" role="toolbar" aria-label="Toolbar to view">
	  <div class="btn-group mr-2" role="group" aria-label="First group">
	    <button type="button" class="btn btn-primary" v-on:click="setViewList(false)" v-bind:class="{ active: !viewList }"
	    data-toggle="tooltip" data-placement="top" title="Visualizza in modalità griglia"
	    >
	    	<i class="fas fa-th-large"></i>
	    </button>
	    <button type="button" class="btn btn-primary" v-on:click="setViewList(true)" v-bind:class="{ active: viewList }" 
	    data-toggle="tooltip" data-placement="top" title="Visualizza in modalità lista"
	    >
	    	<i class="fas fa-th-list"></i>
	    </button>
	  </div>
	</div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
	name: "view-articles",
	props: ['viewList'],
	methods: {
		setViewList(viewList) {
			this.viewList = viewList;

			this.setCookieViewList(viewList);

			/* definito in <app-view-articles @changeView="onChangeView"> */
			this.$emit('changeView', this.viewList); 
		},
		setCookieViewList(cvalue) {
		    this.setCookie("viewList", cvalue);
		},
		setCookie(cname, cvalue) {
		    var d = new Date(); 
		    d.setTime(d.getTime() + 3600 * 1000 * 24 * 365 * 1);  // scade tra un anno
		    var expires = "expires="+ d.toUTCString();
		    console.log(cname + "=" + cvalue + ";" + expires);
		    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}		
	}
};
</script>

<style scoped>
.btn.btn-primary {
	opacity: 0.6;
}
.btn.btn-primary.active {
	opacity: 1;
	background-color: #fa824f !important;
}
</style>
