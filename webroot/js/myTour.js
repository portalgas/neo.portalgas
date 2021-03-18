"use strict";

/*
 * cpn vue non funziona, solo backoffice
 * var objMyTour = null;
 * objMyTour = new MyTour(window.location.href); 
 */
function MyTour(scope) {

    if (!(this instanceof MyTour)) {
        throw new TypeError("MyTour constructor cannot be called as a function.");
    }

    this.init(scope);
}
;

MyTour.prototype = {
    constructor: MyTour, //costruttore
	
	debug: true,
    scope: null,
	tour: null,
    ajaxUrl: '/json/tours/',
	delimiter: '@', // diverso da ; perche' e' il separatore di default dei cookies
	cookie_name: 'tours',
	getTour: function() {
		return this.tour;
	},
	decorateScope: function(scope) {

		scope = scope.replace(document.location.origin, "");
		var newScope = '';
		if(scope.indexOf('/')>-1) {
			var scopeParts = scope.split("/");
			
			// scopeParts[0] = ''
			if(scopeParts[1]=='admin') {
				newScope = scopeParts[1] + '-' + scopeParts[2] + '-' + scopeParts[3]; 
			}
			else
				newScope = scopeParts[1]; 
		}
		console.log('MyTour::decorateScope() scope '+newScope);

		return newScope;
	},
	getScope: function() {
		return this.scope;
	},
    checkCookieTour: function(value) {
		var found = false;
		var values = this.getCookie(this.cookie_name);
		console.log('checkCookieTour get cookie_name '+this.cookie_name);
		if(values=='')
			console.log('not found cookie_name '+this.cookie_name+' => eseguo tour');
		else {
			if(values.indexOf(this.delimiter)>0) {
				var value_splits = values.split(this.delimiter);
				for(var i = 0; i < value_splits.length; i++) 
				{
					if(value_splits[i].toLowerCase() == value.toLowerCase())
						found = true;
				}			
			}
			else {
				if(values.toLowerCase() == value.toLowerCase())
					found = true;
			}			
		}

		return found;
	},
	/*
	 * event: btn CLOSE
	 * acconda i valori del cookies key=value'+this.delimiter+'value...
	 */
	addCookie: function(name, value) {

		var found = false;
		var value_old = this.getCookie(name);
		var value_new = '';
		if(this.debug) console.log("addCookie url corrente " + value);
		if(this.debug) console.log("addCookie oldCookies value " + value_old);

		if(value_old=='') {
			value_new = value;
		}
		else
		if(value_old!='') {
			if(value_old.indexOf(this.delimiter)>0) {
				var value_olds = value_old.split(this.delimiter);
				for(var i = 0; i < value_olds.length; i++)
				{
					if(value_olds[i].toLowerCase() == value.toLowerCase())
						found = true;
				    console.log(value_olds[i]);
				}
			}
			else {
				if(value_old.toLowerCase() == value.toLowerCase())
					found = true;
			}

			if(!found) {
				value_new = value_old + this.delimiter + value;
				value_new = value_new.toLowerCase();		
			}
		} 

		if(!found) {
			if(this.debug) console.log("addCookie newCookies value " + value_new);
			this.setCookie(name, value_new, 365);
		}
		else {
			if(debug) console.log("addCookie value gia' presente ");
		}
	},
	setCookie: function (name, value, exdays) {
	  var d = new Date();
	  d.setTime(d.getTime() + (exdays*24*60*60*1000));
	  var expires = "expires="+ d.toUTCString();
	  document.cookie = name + "=" + value + ";" + expires + ";path=/";
	},
	getCookie: function (name) {
	  var loc_name = name + "=";
	  var decodedCookie = decodeURIComponent(document.cookie);
	  // console.log("getCookie decodedCookie " + decodedCookie);
	  var ca = decodedCookie.split(';');
	  for(var i = 0; i <ca.length; i++) {
	    var c = ca[i];
	    while (c.charAt(0) == ' ') {
	      c = c.substring(1);
	    }
	    if (c.indexOf(loc_name) == 0) {
	      return c.substring(loc_name.length, c.length);
	    }
	  }
	  return "";
	},
    tourStart: function () {
        var _this = this;

		var found = _this.checkCookieTour(_this.scope);
		if(_this.debug) console.log('checkCookieTour found '+found+' per lo scope '+_this.scope);
		if(!found) {			

			var ajaxUrl = _this.ajaxUrl + this.scope + '.json';
			if(_this.debug) console.log('ajaxUrl '+ajaxUrl); 

			$.ajax({url: ajaxUrl, 
			       // data: data, 
			        type: 'GET',
			        dataType: 'json',
			        cache: false,               
			        success: function (response) {
			            if(_this.debug) console.log(response.results.steps);
						// if(_this.debug) console.log('esito '+response.results.esito);			            
			            
			            if(response.results.esito) {
			            	var steps = response.results.steps;

							_this.tour = new Tour({
							    framework: 'bootstrap4',
							    steps: steps,
							    debug: false,
							    showProgressBar: true,      // default show progress bar
							    showProgressText: true,     // default show progress text
							    localization: {
							        buttonTexts: {
							            prevButton: 'Prec.',
							            nextButton: 'Succ.',
							            pauseButton: 'Aspetta',
							            resumeButton: 'Continua',
							            endTourButton: 'Finito!'
							        }
							    },
								backdropOptions:    {
								    highlightOpacity: 0.7,
								    highlightColor: '#000',
									backdropSibling: true,/*
								    animation: {
								        // can be string of css class or function signature: function(domElement, step) {}
								        backdropShow: function(domElement) {
								            domElement.fadeIn();
								        },
								        backdropHide: function(domElement) {
								            domElement.fadeOut("slow")
								        },
								        highlightShow: function(domElement, step) {
								            step.fnPositionHighlight();
								            domElement.fadeIn();
								        },
								        highlightTransition: "tour-highlight-animation",
								        highlightHide: function(domElement) {
								            domElement.fadeOut("slow");
								        }
								    },*/
								},
								onStart: function() {
									console.log("Tour onStart");
								},
								onEnd: function() {
									console.log("Tour onEnd");
									_this.addCookie(_this.cookie_name, _this.scope);
								},															    							    							    
							});

							_this.tour.start();
							_this.tour.restart();
			            }
			        	return true;
			        },
			        error: function (e) {
			            /* 
			             * il file .json puo' non esistere 
			             * console.error(e);
			             */
			            return false;
			        },
			        complete: function (e) {
			        }
			});	

			/* if(_this.debug) console.log('tour end'); */
		} // end if(!found)			
    },
    init: function (scope) {
        
		/*
		 * scope = window.location.href (url completo)
		 */
		scope = scope.toLowerCase();
        console.log('MyTour.init before decorateScope() '+scope);

		if(scope=='')
			return;

        this.scope = this.decorateScope(scope);

        this.tourStart();
    }
};        