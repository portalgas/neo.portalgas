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
		  	errors: [],
			rows: [],
			row: {
    			name: '',
				type: '',
    			value: ''
    		},
    		isFormValid: true
	  },  
	  methods: {
		    addTableRow() {
		   		console.log(this.row);
		        this.rows.push(JSON.parse(JSON.stringify(this.row)));
		        this.row.name = '';
		        this.row.type = 'FIX';
		        this.row.value = '';
		    },
	        removeTableRow: function(index) {
	            this.rows.splice(index, 1);
	        },    	  	
		  	show: function (e) {
		      $('#vue-order-price-types').show('slow');
		    }
	  },
	  mounted: function(){
	  	console.log('mounted vueOrderPriceTypes');
	  }
	});

	vueOrderPriceTypes.show();	
}