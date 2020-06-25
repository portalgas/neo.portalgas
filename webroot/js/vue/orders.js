"use strict";

var vueOrderPriceTypes = null;

window.onload = function () {
	
	var router = new VueRouter({
				mode: 'history',
	    		routes: []
    		});

	var routerOrderPriceTypes = new VueRouter({
				mode: 'history',
	    		routes: []
    		});
	
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

			    console.log(json_price_types); 
		    	if(json_price_types!='')
		    		this.getRowsFromRequest();
		    	else
		    		this.getRowsFromDatabase();
		    },	
		    getRowsFromRequest: function() {
		    	/*
		    	 * variabile json_price_types creata con i valori passati nelle request:
		    	 *	se ho validationErrors ritorno sulla pagina e recupero i valori inseriti
		    	 */
	    		this.addToRows(json_price_types);
		    },	
		    getRowsFromDatabase: function() {	

		        var order_id = $('#id').val();
		        if(order_id=='0' || order_id=='' || typeof order_id === 'undefined')
		           return;
			        
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
}