"use strict";

var vueOrderPriceTypes = null;

$(function () {	

	var routerOrderPriceTypes = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

	vueOrderPriceTypes = new Vue({
	  routerOrderPriceTypes,
	  el: '#vue-order-price-types',
	  data: {
	  		url: '/admin/api/priceTypes/getsByOrderId',
			rows: [], /* rows della tabella */
			row: {    /* singol row della tabella */
    			name: '',
    			descri: '',
				type: 'FIX',
    			value: '',
    			sort: '0'
    		},
            validationErrors: {
                name: null,
                type: null,
                value: null
            },
            spinner_run_type_prices: false,
    		isFormValid: true
	  },  
	  methods: {   	  	
		  	show: function (e) {
		      $('#vue-order-price-types').show('slow');
		    },
		    addTableRow() {

		    	if(!this.validateForm()) {
		    		alert("Dati incompleti");
		    		return;
		    	}

		   		console.log(this.row);
		        this.rows.push(JSON.parse(JSON.stringify(this.row)));

		        this.initRow();
		    },
	        removeTableRow: function(index) {
	            this.rows.splice(index, 1);
	        }, 
	        initRow () {
		        this.row.name = '';
		        this.row.descri = '';
		        this.row.type = 'FIX';
		        this.row.value = '';
		        this.row.sort = '0';
	        },
	        validateForm () {

	        	this.isFormValid = true;

		    	if(this.row.name=='') {
		    		this.isFormValid = false;
		    		this.validationErrors.name = "Campo nome da valorizzare";
		    	}
		    	if(this.row.type=='') {
		    		this.isFormValid = false;
		    		this.validationErrors.type = "Campo tipologia da valorizzare";
		    	}
		    	if(this.row.value=='') {
		    		this.isFormValid = false;
		    		this.validationErrors.value = "Campo valore da valorizzare";
		   		}

		   		return this.isFormValid;	            
	        },
		    getRows: function() {

		    	this.spinnerRun = true;
	            $('.run-type-prices').show();
	            $('.run-type-prices .spinner').addClass(ico_spinner);

		    	if(typeof json_price_types.length === 'undefined')
		    		this.getRowsFromDatabase();
		    	else
		    		this.getRowsFromRequest();
		    },	
		    getRowsFromRequest: function() {
		    	/*
		    	 * variabile json_price_types creata con i valori passati nelle request:
		    	 *	se ho validationErrors ritorno sulla pagina e recupero i valori inseriti
		    	 */
		    	console.log('getRowsFromRequest');  
	    		this.addToRows(json_price_types);

		    	this.spinner_run_type_prices = false;
	            $('.run-type-prices .spinner').removeClass(ico_spinner);
		    },	
		    getRowsFromDatabase: function() {	

		    	console.log('getRowsFromDatabase');
		        var order_id = $('#id').val();
		        if(order_id=='0' || order_id=='' || typeof order_id === 'undefined') {
			    	this.spinner_run_type_prices = false;
		            $('.run-type-prices .spinner').removeClass(ico_spinner);
		            return;
		        }
			        
		        var data = {
		            order_id: order_id
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
		                    this.addToRows(response.results);
		                }
	                },
	                error: function (e) {
	                    console.log(e);
	                    console.log(e.responseText.message);
	                },
	                complete: function (e) {
				    	this.spinner_run_type_prices = false;
			            $('.run-type-prices .spinner').removeClass(ico_spinner);
	                }
	            });     
		    },
		    addToRows: function(results) {
		    	if(results.length==0)
		    		return;

		    	var that = this;
           		$.each(results, function(key, value) {
					let row = {}
					row.name = value.name;
	    			row.descri = value.descri;
	    			row.type = value.type;
	    			row.value = value.value;
	    			row.sort = value.sort;
					that.rows.push(row);						
				});
		    }		    
	  },
	  mounted: function() {
	  	console.log('mounted vueOrderPriceTypes');
	  	this.getRows();
	  },
      filters: {
        priceTypeLabel(code) {
          if(code) {
            switch(code) {
              case "PERC":
                  code = "Percentuale";
              break;
              case "FIX":
                  code = "Fisso";
              break;
              case "FIX_USER":
                  code = "Fisso per gasista";
              break;
            }
          }
          return code;
        }
      }	  
	});

	vueOrderPriceTypes.show();	
});