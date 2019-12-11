"use strict";

var vueSearch = null;
var vueOfferCollaborators = null;
var vueCollaboratorsActivity = null;

window.onload = function () {
	
	var router = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	// console.log("vue vueSearch");
	
	vueSearch = new Vue({
	  router,
	  el: '#vue-search',
	  data: {
	  	errors: [],
		q: null,
		placeholder: '',
		whatVisibility: false,
		whats: ['OFFERS', 'QUOTES', 'COLLABORATORS'],
		what: 'OFFERS'
	  },  
	  methods: {
	  	show: function (e) {
	      $('#vue-search').show('slow');
	    },
	    reverseMessage: function () {
	      this.q = this.q.split('').reverse().join('')
	    },
	    toogleWhat: function (e) {
	    	// console.log('toogleWhat');
	        this.whatVisibility = true;
	        // this.whatVisibility = !this.whatVisibility;
	    },
	    setPlaceholder: function (e) {
	      // console.log(this.$refs);
	      const radio = this.$refs['itemWhat'+this.what];
	      this.placeholder = radio.attributes['data-attr-placeholder'].value;

	      $('#q').attr('placeholder', this.placeholder);	      	
	    },
		checkForm: function(e) {
			this.errors = [];

			if (!this.q) {
				this.errors.push('Indica cosa ricercare');
				//this.$refs.q.focus();
			}
			if (!this.what) {
				this.errors.push('Indica la categoria che desideri ricercare');
			}
			if (!this.whats.includes(this.what)) {
	            this.errors.push('Categoria scelta ('+this.what+') non valida!');
	        }

			if (!this.errors.length) {
				return true;
			}

			e.preventDefault();			
    	},
    	getQueryParameters() {
    		// console.log(this.$route);

    		var qQuery = '';
    		var path = this.$route.path;               // /admin/offers
    		path = path.substring(7).toUpperCase();    // OFFERS
			switch(path) {
			  case 'OFFERS':
			  case 'QUOTES':
			    qQuery = this.$route.query.code;
			  break;
			  case 'COLLABORATORS':
			    qQuery = this.$route.query.name;
			  break;
			  default:
			}

			switch(path) {
			  case 'OFFERS':
			  case 'QUOTES':
			  case 'COLLABORATORS':
			    this.whatVisibility = true;
			    this.q = qQuery;
			    this.what = path;
			  break;
			    this.whatVisibility = true;
			    this.q = qQuery;
			    this.what = path;
			  break;
			  default:
			}
        }
	  },
	  mounted: function(){
	  	console.log('mounted vueSearch');

		this.setPlaceholder();

		this.getQueryParameters();
		/*
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass   : 'iradio_minimal-blue',
	      increaseArea: '20%' // optional
        });
	    $('input[type="checkbox"].minimal, input[type="radio"].minimal').on('ifChecked', function(e){
	      vueSearch.$data.whats.push($(this).val());
	    });
	    $('input[type="checkbox"].minimal, input[type="radio"].minimal').on('ifUnchecked', function(e){
	      let data = vueSearch.$data.whats;
	      data.splice(data.indexOf($(this).val()),1)
	    });*/
	  }, 

	  computed: {

		  now: function () {
			  var monthNames = [
			    "January", "February", "March",
			    "April", "May", "June", "July",
			    "August", "September", "October",
			    "November", "December"
			  ];		 
			  var date = new Date(); 	
			  var day = date.getDate();
			  var monthIndex = date.getMonth();
			  var year = date.getFullYear();

			  var results = day + ' ' + monthNames[monthIndex] + ' ' + year;
			  // console.log(results);

		      return results;
		  }
		}

	});

	vueSearch.show();

	/*
	 * collaborators offerDetails/add offerDetails/edit
	 */
	var routerCollaborators = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	// console.log("vue vueOfferCollaborators");
	
	vueOfferCollaborators = new Vue({
	  routerCollaborators,
	  el: '#vue-offer-collaborators',
	  data: {
		  	errors: [],
			collaboratorSelected: [],
			collaboratorsOptions: [],
			priceTypesOptions: [],
			tableRows: [],
			priceTypeId: '',
			priceTypeName: '',
			priceId: '',
			priceName: '',
			checkboxId: '',
			checkboxName: '',
			da_calcolare: 3
	  },  
	  methods: {
	  	show: function (e) {
	      $('#vue-offer-collaborators').show('slow');
	    },	  	
		listCollaborators(exclude_ids) {
			var _this = this;
		    var offer_id = $('input[name=offer_id]').val();
		    if(offer_id=='')
		    	return; 

	        var data = {
	      		id: offer_id,
	      		exclude_ids: exclude_ids,
	      		entity: 'collaborators'
	        };
	        // console.log(data);
	        // console.log('csrfToken '+csrfToken);

		    var url = '/admin/api/getList';
		    $.ajax({
		    	url: url,
		    	data: data,
                method: 'POST',
                dataType: 'json',
                cache: false,
                headers: {
                  'X-CSRF-Token': csrfToken
                },
	            success: function (response) {
                    _this.collaboratorsOptions = response;
                    // $('#collaborator_id').select2();
					/*
					jQuery.each(response, function(k, v) {
					    console.log(k+' '+v);
					});
	                */
	            },
                error: function (e) {
                    console.log(e.responseText.message);
                } 
		  	});
		}, // if(typeof e.target.options[e.target.options.selectedIndex].text != undefined) {
		addTableRow: function (e) {
			console.log(e);
			if (event.isTrusted) { // per evitare che venga eseguito quando si cancella il collaboratore selezionato
				var _this = this;
				const id = e.target.value;
				const name = e.target.options[e.target.options.selectedIndex].text;
				// console.log('selectedIndex '+e.target.options.selectedIndex+' id '+id+' '+name);

				jQuery.each(_this.collaboratorsOptions, function(k, v) {
					if(k==id)
						delete _this.collaboratorsOptions[id];    
				});

		        _this.priceTypesOptions = priceTypes;

		        const data = {
		        	id: id, 
		        	name: name, 
		        	priceTypeId: 'price-type-id-'+id, 
		        	priceTypeName: 'offer_detail_collaborators.price_type_id['+id+']',
		        	priceId: 'price-'+id, 
		        	priceName: 'offer_detail_collaborators.price['+id+']',
					checkboxId: 'checkbox-collaborator-id-'+id,
					checkboxName: 'checkbox_offer_detail_collaborator.id['+id+']'
		        }
				_this.tableRows.push(data);
			}
		},
		removeTableRow: function(row){
			//console.log 3(row);
			this.tableRows.$remove(row);
		},
		tooglePriceCollaborator: function (e) {
	        const value = e.target.value;
	        const data_att_id = e.target.getAttribute('data-attr-id');
	        if(value==this.da_calcolare) {
	            $('#price-'+data_att_id).show();
	        }
	        else {
	            $('#price-'+data_att_id).hide();
	        }				  	
		}				  	
	  },
	  mounted: function(){
	  	console.log('mounted vueOfferCollaborators');
	  	var _exclude_ids = '';
	  	if (typeof exclude_ids !== 'undefined') 
	  		_exclude_ids = exclude_ids;

	  	this.listCollaborators(_exclude_ids);
	  	objScript.checkboxCheckedTohidden('checkbox_offer_detail_collaborator');
	  }, 
	  computed: {
	  }

	});

	vueOfferCollaborators.show();	


	/*
	 * collaborators filtrati per attivita'
	 */
	var routerCollaboratorsActivity = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	// console.log("vue vueCollaboratorsActivity");
	
	vueCollaboratorsActivity = new Vue({
	  routerCollaboratorsActivity,
	  el: '#vue-collaborators-activity',
	  data: {
		  	errors: [],
			collaboratorSelected: [],
			collaboratorsOptions: [],
	  },  
	  methods: {
	  	show: function (e) {
	    },	  	
		listCollaboratorsByActivity() {
			var _this = this;
		    var collaborator_activity_id = $("#collaborator-activity-id").val();
		    if(collaborator_activity_id=='')
		    	return; 

	        var data = {
	      		collaborator_activity_id: collaborator_activity_id,
	        };
	         console.log(data);
	        // console.log('csrfToken '+csrfToken);

		    var url = '/admin/api/getCollaboratorsListsByActivity';
		    $.ajax({
		    	url: url,
		    	data: data,
                method: 'POST',
                dataType: 'json',
                cache: false,
                headers: {
                  'X-CSRF-Token': csrfToken
                },
	            success: function (response) {
                    _this.collaboratorsOptions = response;
					/*
					jQuery.each(response, function(k, v) {
					    console.log(k+' '+v);
					});
	                */
	                $('#collaborator_id').select2();
	            },
                error: function (e) {
                    console.log(e.responseText.message);
                } 
		  	});
		}, // if(typeof e.target.options[e.target.options.selectedIndex].text != undefined) {
	  },
	  mounted: function(){
	  	console.log('mounted vueCollaboratorsActivity');
	  	if($('#vue-collaborators-activity').length>0)
		  	this.listCollaboratorsByActivity();
	  }, 
	  computed: {
	  }

	});

	vueCollaboratorsActivity.show();	
}