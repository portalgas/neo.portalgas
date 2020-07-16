 "use strict";

function OrganizationsPaysIndex() {

    if (!(this instanceof OrganizationsPaysIndex)) {
        throw new TypeError("OrganizationsPaysIndex constructor cannot be called as a function.");
    }

    this.init();
}
;

OrganizationsPaysIndex.prototype = {
    constructor: OrganizationsPaysIndex, //costruttore
    fieldUpdateAjaxUrl: '/admin/api/OrganizationsPays/setMsgText',
    ico_spinner: 'fa-lg fa fa-spinner fa-spin',
    ico_ok: 'fa-lg text-green fa fa-thumbs-o-up',
    ico_ko: 'fa-lg text-red fa fa-thumbs-o-down',

    bindEvents: function () {
        var _this = this;

        console.log("OrganizationsPaysIndex bindEvents");

        /*
         * aggiorna il DB con il valore del campo
         */
        $('.customFieldUpdateAjax').change(function (e) {

            e.preventDefault();

            var organization_pay_id = $(this).attr('data-attr-id'); 

            if (typeof organization_pay_id === 'undefined') {
                console.log('customFieldUpdateAjax data-attr-id undefined!');
                return;
            }
                 
            var value = '';
            var type = $(this).attr('type');
            // console.log('type '+type);
            switch(type) {
              case 'checkbox':
                // console.log('checked '+$(this).is(':checked'));
                if($(this).is(':checked'))
                    value = 1;
                else
                    value = 0;
              break;
              default:
                value = $(this).val();
            } 

            var entity = 'OrganizationsPays';
            var responseHtml = $('#'+entity+'-'+organization_pay_id);
            if (typeof responseHtml === 'undefined') 
                console.log('customFieldUpdateAjax responseHtml ['+'#'+entity+'-'+organization_pay_id+'] undefined!');
            else
                console.log('customFieldUpdateAjax responseHtml ['+'#'+entity+'-'+organization_pay_id+']');
            $('#'+entity+'-'+organization_pay_id).addClass(_this.ico_spinner);

            var data = {
                organization_pay_id: organization_pay_id,
                value: value,
            };
            console.log(data);

            $.ajax({url: _this.fieldUpdateAjaxUrl, 
                    data: data, 
                    method: 'POST',
                    dataType: 'json',
                    cache: false,
                    headers: {
                      'X-CSRF-Token': csrfToken
                    },                
                    success: function (response) {
                        console.log(response);
                        if (response.code) {
                        }
                        
                        responseHtml.removeClass(_this.ico_spinner);
                        responseHtml.addClass(_this.ico_ok);

                        alert(response.message);
                    },
                    error: function (e) {
                        console.log(e.responseText);
                        responseHtml.removeClass(_this.ico_spinner);
                        responseHtml.addClass(_this.ico_ko);
                    },
                    complete: function (e) {
                        setTimeout( function() {responseHtml.removeClass(_this.ico_ok).removeClass(_this.ico_ko);} , 5000);
                    }
                });                     
        }); 
    },
    init: function () {
        console.log("OrganizationsPaysIndex.init");

        this.bindEvents();
    }
};        

var organizationsPaysIndex = null;
$(function () {
   organizationsPaysIndex = new OrganizationsPaysIndex();       
});  