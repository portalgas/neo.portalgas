 "use strict";

function Movements() {

    if (!(this instanceof Movements)) {
        throw new TypeError("Movements constructor cannot be called as a function.");
    }

    this.init();
}
;

Movements.prototype = {
    constructor: Movements, //costruttore
    
    bindEvents: function () {
        let _this = this;

        console.log("Movements bindEvents");

        $('select[name="movement_type_id"]').on('change', function(e) {
            let movement_type_id = $(this).val();
            _this.gestBox(movement_type_id);
        });  

        $('#frm').on('submit', function(e) { 
            // e.preventDefault();

            let movement_type_id = $('select[name="movement_type_id"]').val();
            console.log(movement_type_id, 'submit() movement_type_id');
            if(movement_type_id=='') {
                alert("Devi scegliere la tipologia di movimento di cassa");
                return false;
            }
            
            switch(movement_type_id) {
                case '3': // Sconto al fornitore
                case '4': // Accredito dal fornitore
                    let supplier_organization_id = $('#supplier_organization_id').val();
                    if(supplier_organization_id=='') {
                        alert("Devi scegliere un produttore");
                        return false;
                    }                    
                break;
                    $('#box-users').hide();
                    $('#box-suppliers').hide();
                break;
                break;
                case '6': // Rimborso Gasista
                case '7': // Movimento di cassa
                    let user_id = $('#user_id').val();
                    if(user_id=='') {
                        alert("Devi scegliere un gasista");
                        return false;
                    }   
                break;
            }

            let payment_type = $('input[name="payment_type"]:checked').val();
            if(typeof payment_type==='undefined' || payment_type=='') {
                alert("Devi scegliere il metodo di pagamento");
                return false;
            }  


            return true;	
        });        
    },
    gestBox: function (movement_type_id) {
        
        // console.log("movement_type_id "+movement_type_id);

        $('#box-users').hide();
        $('#box-suppliers').hide();

        switch(movement_type_id) {
            case '1': // Spesa del G.A.S.
            case '2': // Entrata del G.A.S.
                $('#box-users').hide();
                $('#box-suppliers').hide();
            break;
            case '3': // Sconto al fornitore
            case '4': // Accredito dal fornitore
                $('#box-users').hide();
                $('#box-suppliers').show();
            break;
            case '5': // Pagamento fattura a fornitore
                $('#box-users').hide();
                $('#box-suppliers').hide();
            break;
            break;
            case '6': // Rimborso Gasista
            case '7': // Movimento di cassa
                $('#box-users').show();
                $('#box-suppliers').hide();
            break;
        }        
    },
    init: function () {
        console.log("Movements.init");

        this.bindEvents();

        let movement_type_id = $('select[name="movement_type_id"]').val();
        this.gestBox(movement_type_id);
    }
};        

var movements = null;
$(function () {
   movements = new Movements();       
});  