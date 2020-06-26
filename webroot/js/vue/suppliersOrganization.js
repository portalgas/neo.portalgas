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
	  		url_by_id: '/admin/api/SuppliersOrganizations/getById',
	  		url_by_order_id: '/admin/api/SuppliersOrganizations/getByOrderId',
            spinner_run_supplier_organization: false,
            supplier_organization: {
            	name: null,
            	img1: null,
            	owner_articles: null,
            	supplier: {
            		img1: null
            	}
            }
	  },  
	  methods: {   	  	
		  	show: function (e) {
		      $('#vue-supplier-organization').show('slow');
		    },
	        getSuppliersOrganization: function() {

	        	/*
	        	 * chi gestisce il listino articoli
	        	 * se e' valorizzato order_id => order.edit
	        	 * 		owner_articles da Orders (deriva da SuppliersOrganizations)
	        	 * se non e' valorizzato order_id => order.add
	        	 * 		owner_articles da SuppliersOrganizations
	        	 */
		    	console.log('getSuppliersOrganization');
		        var order_id = $('#order_id').val();
		    	if(order_id=='0' || order_id=='' || typeof order_id === 'undefined') {
					
					var supplier_organization_id = $('#supplier-organization-id').val();
			        if(supplier_organization_id=='0' || supplier_organization_id=='' || typeof supplier_organization_id === 'undefined') {
				    	this.spinner_run_supplier_organization = false;
			            $('.run-supplier-organization .spinner').removeClass(ico_spinner);
			            return;
			        }

			        var data = {
			            supplier_organization_id: supplier_organization_id
			        };
			    	this._getSuppliersOrganization(this.url_by_id, data);
			    }
			    else {
			        var data = {
			            order_id: order_id
			        };			    	
			    	this._getSuppliersOrganization(this.url_by_order_id, data);
			    }
			},
			_getSuppliersOrganization: function(url, data) {
	        	var that = this;
			        
		        console.log(data);

		        $.ajax({url: url, 
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
		                    that.supplier_organization.supplier.img1 = response.results.supplier.img1;
		                    if(typeof response.results.order === 'undefined')
			                    that.supplier_organization.owner_articles = response.results.owner_articles;
			                else
								that.supplier_organization.owner_articles = response.results.order.owner_articles;
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