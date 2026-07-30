<template>

	<main id="accordion-deliveries">

		<div v-if="isLoading" class="box-spinner">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-else>
            <section v-if="organization!=null">	
				<div class="row row-header">
					<div class="col-md-8">
						Consegne
					</div>
					<div class="col-md-4" style="text-align:right;">
						<Organizations :slug="slugGas" :organization="organization" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
                        <Menu :slugGas="slugGas"></Menu>
                    </div>					
					<div class="col-md-10">

						<tabs>
							<DeliveriesType :tab_id="'all0'" :name="'Consegne dal '+dataCinqueGiorniFa" :url="'/api/deliveries/gets?all=0'" :selected="true" :organization="organization" />
							<DeliveriesType :tab_id="'all1'" :name="'Tutte le consegne'" :url="'/api/deliveries/gets?all=1'" :organization="organization" />
						</tabs>
						
					</div> <!-- col-md-12 -->
				</div> <!-- row -->

			</section>
		</div>

	</main> 
</template>

<script>
import Menu from "../components/cms/Menu.vue";
import Organizations from "../components/common/Organizations.vue";
import Tabs from "../components/part/Tabs.vue";
import DeliveriesType from "./DeliveriesType.vue";

export default {
  name: "deliveries",
  components: {
	Menu: Menu,
	Organizations: Organizations,
    Tabs,
    DeliveriesType
  },
  data() {
    return {
	  slugGas: null,
	  organization: null,
      isLoading: false
    };
  }, 
  mounted() {
    // console.log('mounted gas');
    // console.log('slugGas '+this.$route.params.slugGas);
    this.slugGas = this.$route.params.slugGas;
    if(this.slugGas=='')
        return;

    this.getOrganization();
  },
  methods: { 
	getOrganization:function() {
		this.isLoading = true;

		let url = "/api/gas/organization/"+this.slugGas;
		// console.log(url, 'getOrganization url');
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
	computed: {
		dataCinqueGiorniFa() {
			const d = new Date();
			d.setDate(d.getDate() - 5);

			const opzioni = { 
				weekday: 'long',  // Nome completo del giorno (es. "lunedì")
				day: 'numeric',   // Giorno in numero (es. "24")
				month: 'long',    // Nome completo del mese (es. "luglio")
				year: 'numeric'   // Anno (es. "2026")
			};

			// Formattazione in italiano
			const dataStringa = d.toLocaleDateString('it-IT', opzioni);

			// Prima lettera maiuscola (opzionale: es. "Lunedì 24 luglio 2026")
			return dataStringa.charAt(0) + dataStringa.slice(1);
		}
	}
};
</script>

<style scoped>
.row-header {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  padding: 10px;
  background: none repeat scroll 0 0 #1e83c2;
  color: #fff;
}
</style> 