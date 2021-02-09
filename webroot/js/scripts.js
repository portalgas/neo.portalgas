"use strict";

function Script() {

    if (!(this instanceof Script)) {
        throw new TypeError("Script constructor cannot be called as a function.");
    }

    this.init();
}
;

Script.prototype = {
    constructor: Script, //costruttore
    fieldUpdateAjaxUrl: '/admin/api/fieldUpdate',
    da_calcolare: 3,
    ico_spinner: 'fa-lg fa fa-spinner fa-spin',
    ico_ok: 'fa-lg text-green fa fa-thumbs-o-up',
    ico_ko: 'fa-lg text-red fa fa-thumbs-o-down',

    bindEvents: function () {
        var _this = this;

        console.log("Script bindEvents");

        $(".price-type-id").change(function (e) {
            _this.tooglePriceCollaborator(this);
        });

        $('.importo').change(function (e) {
            _this.formatImport(this);
        });

        /*
         * gestione img per helps
         */
        $('.img-helps').click(function () {
            var src = $(this).find('img').attr('src');
            var img = '<img src="'+src+'" />';
            var title = $(this).attr('data-attr-title');
            /* console.log(title+' '+img); */
            $('#modalHelps .modal-body').html(img);
            $('#modalHelps .modal-title').html(title);
        });

        /*
         * gestione img per helps
         */
        $('.ctrl-length').each(function( index ) {
             var value = $(this).val();
             $(this).after('<p style="float:right" class="avviso">Hai ancora <strong>'+ (orderNotaMaxLen - value.length) +'</strong> caratteri disponibili</p>');
        });

        $('.ctrl-length').keyup(function() {
            if($(this).val().length > orderNotaMaxLen) {
                $(this).val($(this).val().substr(0, orderNotaMaxLen));
            }
            $(this).parent().find('p.avviso').html("Hai ancora <strong>"+ (orderNotaMaxLen - $(this).val().length)+"</strong> caratteri disponibili");
        });

        /*
         * + / - accordion 
         */
        $('.collapse').on('shown.bs.collapse', function (e) {
            $(this).parent().find(".fa-plus").removeClass('fa-plus').addClass('fa-minus');
        }).on('hidden.bs.collapse', function () {
            $(this).parent().find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
        });

        $('.onFocusAllSelect').focus(function (e) {
            $(this).select();
        }); 

        /*
         * aggiorna il DB con l'ico true/flase
         */
        $('.fieldUpdateAjax').click(function (e) {
            console.log('fieldUpdateAjax click()');
            _this.fieldUpdateAjax(this);
        }); 

        /*
         * aggiorna il DB con il valore del campo
         */
        $('.fieldUpdateAjax').change(function (e) {
            console.log('fieldUpdateAjax change()');
            e.preventDefault();
            _this.fieldUpdateAjax(this);               
        }); 

        $(".navbar .menu").slimscroll({
            height: "200px",
            alwaysVisible: false,
            size: "3px"
        }).css("width", "100%");
        
        /* 
         * Initialize Select2 Elements https://select2.github.io/
         */
        $('.select2').select2();

        /*
         * iCheck for checkbox and radio inputs http://fronteed.com/iCheck/
        conflitto con Vue, settato in search.ctp
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass   : 'iradio_minimal-blue'
        });
        */

        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            dateFormat: 'dd/mm/yyyy',
            language: 'it',
            changeMonth: true,
            changeYear: true,
            showAnim: 'slideDown', 
            changeMonth: true, 
            changeYear: true,
            yearRange: (_this.year - 110) + ":" + (_this.year + 1),
            todayBtn: "linked",
            todayHighlight: true      
        });

        // se .daterangepicker mi prende il css /admin_l_t_e/bower_components/bootstrap-daterangepicker/daterangepicker.css
        $('.bootstrap-daterangepicker').daterangepicker({
          locale: {
            format: 'DD/MM/Y'
          },
          autoUpdateInput: false,  // per evitare che venga subito eseguito fieldUpdateAjax
          isInvalidDate: function(ele) {
              var currDate = moment(ele._d).format('DD/MM/Y');
              return ["01-01-2019"].indexOf(currDate) != -1;
          }          
          //startDate: moment().subtract(3, 'month'), 
          //endDate: moment().subtract(1, 'month'),  
        });        
    },
    /*
     * aggiorna il DB con il valore gestiti da data-attr
     */
    fieldUpdateAjax: function (obj) {

        var _this = this;
        var _obj = obj;

        var id = $(obj).attr('data-attr-id');
        var entity = $(obj).attr('data-attr-entity');
        var field = $(obj).attr('data-attr-field');

        if (typeof id === 'undefined') {
            console.log('fieldUpdateAjax data-attr-id undefined!');
            return;
        }
             
        if (typeof entity === 'undefined') {
            console.log('fieldUpdateAjax data-attr-entity undefined!');
            return;
        }
             
        if (typeof field === 'undefined') {
            console.log('fieldUpdateAjax data-attr-field undefined!');
            return;                
        }

        var value = '';
        var type = $(obj).attr('type');
        // console.log('type '+type);
        if (typeof type === 'undefined') 
            type = 'icon-true-false';

        switch(type) {
          case 'checkbox':
            // console.log('checked '+$(this).is(':checked'));
            if($(obj).is(':checked'))
                value = 1;
            else
                value = 0;
          break;
          case 'icon-true-false':
                value = $(obj).attr('data-attr-value');
                value=='1' ? value='0' : value='1';
          break;
          default:
            value = $(obj).val();
        } 

        $(obj).after('<div class="response-ajax" id="'+entity+'-'+id+'"></div>'); 
        var responseHtml = $('#'+entity+'-'+id);
        if (typeof responseHtml === 'undefined') 
            console.log('fieldUpdateAjax responseHtml ['+'#'+entity+'-'+id+'] undefined!');
        else
            console.log('fieldUpdateAjax responseHtml ['+'#'+entity+'-'+id+']');
        responseHtml.addClass(_this.ico_spinner);

        var data = {
            id: id,
            entity: entity,
            field: field,
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

                    if(type=='icon-true-false') {

                        var value = $(_obj).data('attr-value');
                        if(value==0) {
                            $(_obj).data('attr-value', '1');
                            $(_obj).attr('data-attr-value', '1'); 
                        }
                        else
                        if(value==1) { 
                            $(_obj).data('attr-value', '0'); 
                            $(_obj).attr('data-attr-value', '0'); 
                        }
                                                
                        if($(_obj).hasClass('glyphicon-ok'))
                            $(_obj).removeClass('glyphicon-ok').removeClass('icon-true').addClass('glyphicon-remove').addClass('icon-false');
                        else 
                        if($(_obj).hasClass('glyphicon-remove'))
                            $(_obj).removeClass('glyphicon-remove').removeClass('icon-false').addClass('glyphicon-ok').addClass('icon-true');
                        else 
                        if($(_obj).hasClass('fa-lock'))
                            $(_obj).removeClass('fa-lock').removeClass('icon-true').addClass('fa-unlock-alt').addClass('icon-false');                        
                        else 
                        if($(_obj).hasClass('fa-unlock-alt'))
                            $(_obj).removeClass('fa-unlock-alt').removeClass('icon-false').addClass('fa-lock').addClass('icon-true');
                    }
                },
                error: function (e) {
                    console.log(e.responseText.message);
                    responseHtml.removeClass(_this.ico_spinner);
                    responseHtml.addClass(_this.ico_ko);
                },
                complete: function (e) {
                    setTimeout( function() {responseHtml.removeClass(_this.ico_ok).removeClass(_this.ico_ko);} , 2500);
                }
            });
    },      
    tooglePriceCollaborator: function (obj) {
        var _this = this;
        var value = $(obj).val();
        var data_target = $(obj).attr('data-target');
        console.log('tooglePriceCollaborator '+value+' data_target '+data_target);
        if(value==_this.da_calcolare) {
            $(data_target).show();
        }
        else {
            $(data_target).hide();
            // $(data_target).val('0,00');
        }
    },     
    /*
     * prende gli id nel div passato e lo concatena in ids, all'evento change di un chekbox
     */
    bindEventCheckedId: function (field_id) {

        $("." + field_id).change(function () {
            $("#" + field_id + "s").val("");
            var values = "";

            $("input[name=" + field_id + "]").each(function (index) {
                if (this.checked) {
                    var id = $(this).val();
                    values += id + ",";
                }
            });
            if (values != "")
                values = values.substring(0, (values.length - 1));

            /* console.log("bindEventCheckedId "+values); */

            $("#" + field_id + "s").val(values);
        });
    },
    bindEventCheckedAll: function (field_class) {
        var _this = this;
        $("input[name=" + field_class + "-s]").click(function () {

            /*
             * gestione label
             var current_label = $("label[for='"+field_class+"']").text();
             var next_label = $(this).attr('data-attr-label');
             $(this).attr('data-attr-label', current_label);
             
             var html = $("label[for='"+field_class+"']").html();
             var pos = html.lastIndexOf(current_label);
             */
            // html = html.substring(0, pos)+next_label; bugs mi tiene checked=checked

            var checked = $("input[name=" + field_class + "-s]:checked").val();
            /* console.log("bindEventCheckedAll - input name="+field_class+"-s: "+checked);  */
            if (checked == "ALL") {
                $("." + field_class).prop("checked", true);
            } else {
                $("." + field_class).prop("checked", false);
            }
        });
    },
    /*
     * inserisce nel campo hidden i checkox selezionati separati da virgola all'evento change()
     */ 
    ChangeCheckboxCheckedTohidden: function(prefix_field) {
        var _this = this;

        $('.'+prefix_field).on('change', function() {
            _this.checkboxCheckedTohidden(prefix_field);
        });
    },
	/*
	 * formatta l'importo float che arriva dal database
	 */
    formatImportToDb: function(obj) {
        let value = $(obj).val();
        console.log('formatImportToDb BEFORE value '+value);
        value = this.numberFormat(value, 2, ',', '.');
        console.log('formatImportToDb AFTER value '+value);
        $(obj).val(value);
    },
    /* 
     * formatta data da inserimento utente
     */
    formatImport: function(obj) {
        let value = $(obj).val();
        console.log('formatImport BEFORE.replace value '+value);
        value = value.replace('.', '');
        value = value.replace(',', '.');
        console.log('formatImport BEFORE value '+value);
        value = this.numberFormat(value, 2, ',', '.');
        console.log('formatImport AFTER value '+value);
        $(obj).val(value);
    },
    numberFormat: function (number, decimals, dec_point, thousands_sep) {
        /* da 1000.5678 in 1.000,57 */
        /* da 1000 in 1.000,00 */

        var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
        var d = dec_point == undefined ? "." : dec_point;
        var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
        var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    },
    /*
     * inserisce nel campo hidden i checkox selezionati separati da virgola => evento submit()
     */ 
    checkboxCheckedTohidden: function(prefix_field) {
        var _this = this;

        var ids='';
        $('input[name="'+prefix_field+'.ids"]').val(ids);
        
        $('.'+prefix_field).each(function(index) {
            
            if($(this).is(':checked')) {
                ids = ids + $(this).val() + ',';
                // console.log(index+' CHECKED value '+$(this).val()+' ids '+ids);
                console.log(index+' CHECKED value '+$(this).val()+' ids '+ids);
            }
            else {
                // console.log(index+' NOT CHECKED value '+$(this).val()+' ids '+ids);
                console.log(index+' NOT CHECKED value '+$(this).val()+' ids '+ids);
            }
        });
                
        if(ids!='')
            ids = ids.substring(0, ids.length-1);

        $('input[name="'+prefix_field+'.ids"]').val(ids);
    },
    /*
     * estrae l'eventuale ancora nell'url corrente
     */
    getUrlAnchor: function() {
        var url = window.location.href;
        var anchor = '';
        if(url.indexOf("#")>0) {
            anchor = url.substring(url.indexOf("#")+1);
        }
        return anchor;
    },
    /*
     * ping per mantenere la session
     */
    ping: function(pingAjaxUrl, pingTime) {
        var _this = this;
        var _pingAjaxUrl = pingAjaxUrl;
        var _pingTime = pingTime;
        /* console.log("Script.ping "+pingAjaxUrl+' time '+pingTime); */
        window.setInterval(_this.callPing, pingTime, _pingAjaxUrl);
    },
    callPing: function(pingAjaxUrl) {
        /* console.log("Script.callPing "+pingAjaxUrl); */
        var httpRequest = new XMLHttpRequest();
        httpRequest.open('GET', pingAjaxUrl);
        httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        httpRequest.setRequestHeader("Content-type", "application/json");
        httpRequest.setRequestHeader('X-CSRF-Token', csrfToken);
        httpRequest.send(null);
    },
    init: function () {
        console.log("Script.init");
        var _this = this;

        _this.year = new Date().getFullYear();
        _this.bindEvents();

        $('.importo').each(function(index) {
            _this.formatImportToDb(this);
        });        

        $('.price-type-id').each(function(index) {
            _this.tooglePriceCollaborator(this);
        });
    }
};        
