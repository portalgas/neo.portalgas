"use strict";

function Mapping() {

    if (!(this instanceof Mapping)) {
        throw new TypeError("Mapping constructor cannot be called as a function.");
    }

    this.init();
}
;

Mapping.prototype = {
    constructor: Mapping, //costruttore
    MappingTypeIdInnerTableParent: 5,
    MappingTypeIdDate: 3,
    MappingTypeIdDateTime: 4,

    bindEvents: function () {
        var _this = this;

        console.log("Mapping bindEvents");

        $("#mapping-type-id").change(function (e) {
            _this.toogleMappingTypeId();
        });

        $("#frm").on('submit', function() {
            return _this.formSubmit();
        }); 
    },
    formSubmit: function() {
        return true;
    },
    toogleMappingTypeId: function () {
        var _this = this;
        var value = $("#mapping-type-id").val();
        // console.log(value);
        if(value==_this.MappingTypeIdInnerTableParent) {
            $('.box-mapping-type-id-value').hide();
            $('.box-mapping-type-id-inner-table-parent').show();            
        }
        else
        if(value==_this.MappingTypeIdDate) {
            $('.box-mapping-type-id-value').hide();
            $('.box-mapping-type-id-inner-table-parent').hide();            
        }
        else
        if(value==_this.MappingTypeIdDateTime) {
            $('.box-mapping-type-id-value').hide();
            $('.box-mapping-type-id-inner-table-parent').hide();          
        }
        else {
            $('.box-mapping-type-id-inner-table-parent').hide();
            $('.box-mapping-type-id-value').show();            
        }     
    },    
    init: function () {
        console.log("Mapping.init");

        this.bindEvents();

        this.toogleMappingTypeId();
    }
};

var objMapping = null;
$(function () {
   objMapping = new Mapping();       
});  