 "use strict";

function OrdersForm() {

    if (!(this instanceof OrdersForm)) {
        throw new TypeError("OrdersForm constructor cannot be called as a function.");
    }

    this.init();
}
;

OrdersForm.prototype = {
    constructor: OrdersForm, //costruttore
    fieldUpdateAjaxUrl: '/admin/api/OrganizationsPays/setMsgText',
    ico_spinner: 'fa-lg fa fa-spinner fa-spin',
    ico_ok: 'fa-lg text-green fa fa-thumbs-o-up',
    ico_ko: 'fa-lg text-red fa fa-thumbs-o-down',

    bindEvents: function () {
        var _this = this;

        console.log("OrdersForm bindEvents");

        /*
         * gestione tipoligie di consegna (typeDeliverySelect)
         *  Y => select consegne attive
         *  delivery_id => Da definire
         *  TO-CREATE => mail o link per nuova consegna
         */
        $('.content.delivery input[type="radio"]').on('click', function(e) {

            let typeDeliverySelect = $(this).val(); 
            console.log(typeDeliverySelect, 'typeDeliverySelect');
            
            $('.content.delivery .radio-delivery-type').css('opacity', '0.3');
            $(this).next().css('opacity', 1);

            _this.gestTypeDelivery(typeDeliverySelect);
        });  
        
        $('.sendMail').on('click', function(e) { 
            e.preventDefault();           
            $("#dialog-send_mail").modal();
            return false;	
        });

        $('#submit-modal-mail').on('click', function(e) {
            e.preventDefault();
            var mail_body = $('#mail_body').val();
            if(mail_body=="") {
                alert("Devi indicare il testo della mail");
                return false;
            }            
    
            var params = {
                mail_body: mail_body
            }
            /* console.log(params, 'params'); */
                    
            var url = '';
            url = '/admin/api/mails/request-delivery-new';
    
            $.ajax({
                type: "POST",
                url: url,
                data: params,
                cache: false,
                headers: {
                  'X-CSRF-Token': csrfToken
                }, 
                success: function(response) {
                    $("#dialog-send_mail").modal("hide");
                    alert("Mail inviata");
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.error(XMLHttpRequest.responseText, 'responseText');
                    $("#dialog-send_mail").modal("hide");
                    alert("Mail non inviata");
                }
            });
                
            return false;
        });          

    },
    gestTypeDelivery: function (typeDeliverySelect) {
        
        if(typeDeliverySelect=='N') {
            $('#delivery_ids').removeAttr('disabled');
            $('#radio-delivery-type-N').css('opacity', 1);
            $('#radio-delivery-type-TO-CREATE').hide();
            $('#radio-delivery-type-TO-CREATE-disabled').show();
        }
        else 
        if(typeDeliverySelect=='TO-CREATE') {
            $('#delivery_ids').attr('disabled', true);
            $('#delivery_ids').attr('disabled', 'disabled');
            $('#radio-delivery-type-TO-CREATE').css('opacity', 1);
            $('#radio-delivery-type-TO-CREATE').show();
            $('#radio-delivery-type-TO-CREATE-disabled').hide();
        }
        else {
            // (Da definire)
            $('#delivery_ids').attr('disabled', true);
            $('#delivery_ids').attr('disabled', 'disabled');
            $('#radio-delivery-type-Y').css('opacity', 1);
            $('#radio-delivery-type-TO-CREATE').hide();
            $('#radio-delivery-type-TO-CREATE-disabled').show();
        }   
    },
    init: function () {
        console.log("OrdersForm.init");

        this.bindEvents();
        this.gestTypeDelivery('N');
    }
};        

var ordersForm = null;
$(function () {
   ordersForm = new OrdersForm();       
});  