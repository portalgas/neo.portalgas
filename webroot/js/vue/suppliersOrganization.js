"use strict";

var vueSuppliersOrganization = null;

$(function () {

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
	        	var _this = this;
			        
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
		                    _this.supplier_organization.name = response.results.name;
		                    _this.supplier_organization.img1 = response.results.img1;
		                    _this.supplier_organization.supplier.img1 = response.results.supplier.img1;
		                    if(typeof response.results.order === 'undefined')
			                    _this.supplier_organization.owner_articles = response.results.owner_articles;
			                else
								_this.supplier_organization.owner_articles = response.results.order.owner_articles;
		                }
	                },
	                error: function (e) {
	                    console.error(e);
	                    console.error(e.responseText.message);
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
        currency(amount) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          const amt = Number(amount);
          return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
        },
        /*
         * formatta l'importo float che arriva dal database
         * da 1000.5678 in 1.000,57 
         * da 1000 in 1.000,00          
         */
        formatImportToDb: function(number) {
              var decimals = 2;
              var dec_point = ','; 
              var thousands_sep = '.';

              console.log('formatImportToDb BEFORE number '+number);

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
              console.log('formatImportToDb AFTER number '+number);
              return number;
          },         
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        },
          counter: function (index) {
            return index+1
        },
              	
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
});