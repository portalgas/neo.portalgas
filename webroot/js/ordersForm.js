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
         * Configure::read('Order.type.gas_groups')
         * consegne caricate in base al gruppo
         */
        $('select[name="gas_group_id"]').on('change', function(e) {
            let gas_group_id = $(this).val();
            _this.setGasGroupDeliery(gas_group_id);
        });

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
                    
            let url = '/admin/api/mails/request-delivery-new';
    
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

        $('#frm').on('submit', function(e) { 
            // e.preventDefault();

            let supplier_organization_id = $('select[name="supplier_organization_id"]').val();
            console.log(supplier_organization_id, 'submit() supplier_organization_id');
            if(supplier_organization_id=='') {
                alert("Devi scegliere un produttore");
                return false;
            }
            
            /* 
             * valorizzo delivery_id 
             */
            let typeDeliverySelect = $('.content.delivery input[type="radio"]:checked').val(); 
            let delivery_id = '';
            console.log(typeDeliverySelect, 'submit() typeDeliverySelect');

            $('input[name="delivery_id"]').val('');
            if(typeDeliverySelect=='N') { // menu a tendina con consegne attive
                delivery_id = $('#delivery_ids').val();
                $('input[name="delivery_id"]').val(delivery_id);
            }
            else 
            if(typeDeliverySelect=='TO-CREATE') { // link a richiesta consegna 
                delivery_id = '';
            }
            else { // Data e luogo della consegna ancora da definire
                delivery_id = $('.content.delivery input[type="radio"]:checked').val(); 
                $('input[name="delivery_id"]').val(delivery_id);
            }

            delivery_id = $('input[name="delivery_id"]').val();
            console.log(delivery_id, 'submit() delivery_id');
            if(delivery_id=='') {
                alert("Devi scegliere una consegna");
                return false;	
            }

            return true;	
        });
    },
    setGasGroupDeliery: function (gas_group_id) {
    
        if(gas_group_id=='') {
            $('#gas-group-deliveries').hide();
            return;
        }

        let url = '/admin/api/gas-group-deliveries/gets';
        let params = {
            gas_group_id: gas_group_id
        };

        $.ajax({
            type: "POST",
            url: url,
            data: params,
            cache: false,
            headers: {
            'X-CSRF-Token': csrfToken
            }, 
            success: function(response) {
                $('#gas-group-deliveries').show();
                console.log(response);

                let devlivey_default = $('#delivery-id').val();
                let selected = '';
                $('#delivery-id').html('');
                $.each(response, function(key, value){
                    selected = '';
                    if(devlivey_default==key) selected = 'selected';
                    $('#delivery-id').append('<option value="'+key+'" '+selected+'>'+value+'</option>');
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.error(XMLHttpRequest.responseText, 'responseText');
            }
        });
    },  
    gestTypeDelivery: function (typeDeliverySelect) {
        
        $('input[name="delivery_id"]').val('');
        
        if(typeDeliverySelect=='N') { // menu a tendina con consegne attive
            $('#delivery_ids').removeAttr('disabled');
            $('#radio-delivery-type-N').css('opacity', 1);
            $('#radio-delivery-type-TO-CREATE').hide();
            $('#radio-delivery-type-TO-CREATE-disabled').show();
        }
        else 
        if(typeDeliverySelect=='TO-CREATE') { // link a richiesta consegna 
            $('#delivery_ids').attr('disabled', true);
            $('#delivery_ids').attr('disabled', 'disabled');
            $('#radio-delivery-type-TO-CREATE').css('opacity', 1);
            $('#radio-delivery-type-TO-CREATE').show();
            $('#radio-delivery-type-TO-CREATE-disabled').hide();
        }
        else { // Data e luogo della consegna ancora da definire
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

        let typeDelivery = $('input[name="type_delivery"]:checked').val();
        if(typeDelivery=='') typeDelivery = 'N';
        this.gestTypeDelivery(typeDelivery);

        if($('select[name="gas_group_id"]').length>0) {
            let gas_group_id = $('select[name="gas_group_id"]').val();
            if(gas_group_id!='') 
                $('#gas-group-deliveries').show();
                this.setGasGroupDeliery(gas_group_id);
        }
    }
};        

var ordersForm = null;
$(function () {
   ordersForm = new OrdersForm();       
});  