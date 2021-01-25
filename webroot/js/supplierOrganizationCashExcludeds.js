"use strict";

function SupplierOrganizationCashExcludeds() {

    if (!(this instanceof SupplierOrganizationCashExcludeds)) {
        throw new TypeError("SupplierOrganizationCashExcludeds constructor cannot be called as a function.");
    }

    this.init();
}
;

SupplierOrganizationCashExcludeds.prototype = {
    constructor: SupplierOrganizationCashExcludeds, //costruttore
    ajaxUrl: '/admin/api/cashes/excludedUpdate',
    
    bindEvents: function () {
        var _this = this;

        // console.log("SupplierOrganizationCashExcludeds bindEvents");

        $(".cashExcludeds").click(function (e) {

            e.preventDefault();

            var id = $(this).attr('data-attr-id');
            var type = $(this).attr('data-attr-type'); /* insert-{id} delete-{id} */

            if (typeof id === 'undefined') {
                // console.log('cashExcludeds data-attr-id undefined!');
                return;
            }
                 

            var responseHtml = $('.msg'+'-'+id);
            if (typeof responseHtml === 'undefined') {
                // console.log('cashExcludeds responseHtml [msg-'+id+'] undefined!');
            }
            else {
                // console.log('cashExcludeds responseHtml [msg-'+id+']');
            }
            responseHtml.addClass('fa fa-spinner');

            var data = {
                id: id
            };
            /* console.log(data); */

            $.ajax({url: _this.ajaxUrl, 
                    data: data, 
                    method: 'POST',
                    dataType: 'json',
                    cache: false,
                    headers: {
                      'X-CSRF-Token': csrfToken
                    },                
                    success: function (response) {
                        // console.log(response);
                        if (response.code) {
                        }
                        
                        responseHtml.removeClass('fa-lg fa fa-spinner');
                        responseHtml.addClass('fa-lg text-green fa fa-thumbs-o-up');

                        // console.log(type);
                        if(type=='insert-'+id) {
                            $('[data-attr-type=insert-'+id+']').hide();
                            $('[data-attr-type=delete-'+id+']').show();
                        }
                        else
                        if(type=='delete-'+id) {
                            $('[data-attr-type=delete-'+id+']').hide();
                            $('[data-attr-type=insert-'+id+']').show();
                        }    
                    },
                    error: function (e) {
                        // console.log(e.responseText);
                        // console.log(e.responseText.message);
                        responseHtml.removeClass('fa-lg fa fa-spinner');
                        responseHtml.addClass('fa-lg text-red fa fa-thumbs-o-down');
                    },
                    complete: function (e) {
                        setTimeout( function() {responseHtml.removeClass('fa fa-thumbs-o-up').removeClass('fa fa-thumbs-o-down');} , 5000);
                    }
                });                     
        }); 
    },
    init: function () {
        // console.log("SupplierOrganizationCashExcludeds.init");

        this.bindEvents();
    }
};

var supplierOrganizationCashExcludeds = null;
$(function () {
   supplierOrganizationCashExcludeds = new SupplierOrganizationCashExcludeds();       
});  