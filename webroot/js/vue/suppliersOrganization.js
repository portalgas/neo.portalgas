"use strict";

var vueSuppliersOrganization = null;

window.onload = function () {
	
	var router = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	var routerSuppliersOrganization = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

	vueSuppliersOrganization = new Vue({
	  routerSuppliersOrganization,
	  el: '#vue-supplier-organization',
	  data: {
	  		url: '/admin/api/SuppliersOrganizations/getById',
            spinner_run_supplier_organization: false,
            supplier_organization: {
            	name: null,
            	img1: null,
            	owner_articles: null
            }
	  },  
	  methods: {   	  	
		  	show: function (e) {
		      $('#vue-supplier-organization').show('slow');
		    },
	        getSuppliersOrganization: function() {

	        	var that = this;

		    	console.log('getSuppliersOrganization');
		        var supplier_organization_id = $('#supplier-organization-id').val();
		        if(supplier_organization_id=='0' || supplier_organization_id=='' || typeof supplier_organization_id === 'undefined') {
			    	this.spinner_run_supplier_organization = false;
		            $('.run-supplier-organization .spinner').removeClass(ico_spinner);
		            return;
		        }
			        
		        var data = {
		            supplier_organization_id: supplier_organization_id
		        };
		        console.log(data);

		        $.ajax({url: this.url, 
	                data: data, 
	                method: 'POST',
	                dataType: 'html',
	                cache: false,
	                headers: {
	                  'X-CSRF-Token': csrfToken
	                },                
	                success: function (response) {
	                	response = JSON.parse(response);
	                    console.log(response);
	                    if (response.code==200) {
	                    	console.log(response.results.name);
		                    that.supplier_organization.name = response.results.name;
		                    that.supplier_organization.img1 = response.results.img1;
		                    that.supplier_organization.owner_articles = response.results.owner_articles;
		                }
	                },
	                error: function (e) {
	                    console.log(e);
	                    console.log(e.responseText.message);
	                },
	                complete: function (e) {
				    	this.spinner_run_supplier_organization = false;
			            $('.run-supplier-organization .spinner').removeClass(ico_spinner);
	                }
	            });     
		    }
	  },
	  mounted: function() {
	  	console.log('mounted vueSuppliersOrganization');
	  	this.getSuppliersOrganization();
	  },
      filters: {
        ownerArticlesLabel(code) {
          if(code) {
            switch(code) {
              case "SUPPLIER":
                  code = "Il produttore";
              break;
              case "PACT":
                  code = "Il gestore del patto";
              break;
              case "REFERENT":
                  code = "Il referente del G.A.S.";
              break;
              case "REFERENT-TMP":
                  code = "Temporaneamente il referente del G.A.S.";
              break;
              case "DES":
                  code = "Il titolare D.E.S. del produttore";
              break;
            }
          }
          return code;
        }
      } 
	});

	vueSuppliersOrganization.show();	
}