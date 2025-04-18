"use strict";

var articlesImport = null;

$(function () {

    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    articlesImport = new Vue({
      router,
      el: '#vue-articles-import',
      data: {
        num_excel_fields: 0,
        fields_to_config: 0,
        select_import_fields: [],
        is_first_row_active: false,
        is_first_row_header: false,
        ums: ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'],
        supplier_organization_id: '',
        supplier_organization: {
          id: null,
          name: null,
          img1: null,
          owner_articles: null,
          supplier: {
            img1: null
          }
        },
        file_errors: [],
        file_contents: [],
        file_metadatas: [],
        is_run: true,
        validazioneResults: [],
        uploadResults: [],
        importResult: false
      },
      methods: {
        init: function() {
          this.num_excel_fields = 0;
          this.fields_to_config = 0;
          this.select_import_fields = [];
          this.supplier_organization_id = '';
          this.file_errors = [];
          this.file_contents = [];
          this.file_metadatas = [];
          this.is_run = false;
          this.validazioneResults = [];
          this.uploadResults = [];
        },
        toggleIsFirstRowHeader: function() {
          this.is_first_row_header = !this.is_first_row_header;
        },
        getSuppliersOrganization: function() {

          /*
           * chi gestisce il listino articoli
           * se e' valorizzato order_id => order.edit
           * 		owner_articles da Orders (deriva da SuppliersOrganizations)
           * se non e' valorizzato order_id => order.add
           * 		owner_articles da SuppliersOrganizations
           */
          // console.log('getSuppliersOrganization supplier_organization_id '+this.supplier_organization_id);
          if(typeof this.supplier_organization_id==='undefined') {
            this.init();
            return;
          }

          var _this = this;

          let params = {
            supplier_organization_id: this.supplier_organization_id
          };

          $.ajax({url: '/admin/api/SuppliersOrganizations/getById',
              data: params,
              method: 'POST',
              dataType: 'html',
              cache: false,
              headers: {
                'X-CSRF-Token': csrfToken
              },
              success: async function (response) {
                response = JSON.parse(response);
                  /* console.log(response); */
                  if (response.code==200) {
                    // console.log(response.results.name, '_getSuppliersOrganization');
                    _this.supplier_organization.id = response.results.id;
                    _this.supplier_organization.name = response.results.name;
                    _this.supplier_organization.img1 = response.results.img1;
                    _this.supplier_organization.supplier.img1 = response.results.supplier.img1;
                    _this.supplier_organization.owner_articles = response.results.owner_articles;

                    await setTimeout(() => {
                      _this.setDropzone();
                    }, 10);
                }
              },
              error: function (e) {
                  console.error(e, '_getSuppliersOrganization');
                  if(typeof e.responseText!=='undefined')
                    console.error(e.responseText.message, '_getSuppliersOrganization');
              },
              complete: function (e) {
              }
          });
        },
        /*
         * configurazione ogni colonna dell'xlsx
         */
        setOptionsFields: function(index) {
          let debug = false;
          let _this = this;
          let option_field_select = $('select[name="option-field-'+index+'"]').val();
          if(debug) console.log(import_fields, 'import_fields');
          // console.log(index+' '+option_field_select, 'option_field_select');
          if(debug) console.log(_this.num_excel_fields, '_this.num_excel_fields');

          /*
           * workaround, se la colonna e' imposta a prezzo
           * sostituisco . con ,
          */
         let val = '';
         if(option_field_select=='prezzo') {
            $.each( _this.file_contents, function(num_row, value) {
              val = _this.file_contents[num_row][index];
              if(val!==null) {
                val = val.replace('.', ',').replace('€', '').trim();
                _this.file_contents[num_row][index] = val;
              }
            });
         }
         else
         if(option_field_select=='qta_um' || option_field_select=='um') {
            $.each( _this.file_contents, function(num_row, value) {
              val = _this.file_contents[num_row][index];
              if(val!==null) {
                val = val.replace(',', '.').trim();
                val = val.toUpperCase();
                _this.file_contents[num_row][index] = val;
              }
            });
         }

          /*
           * x ogni select estraggo il valore scelto
           */
          let select = '';
          _this.select_import_fields = [];
          for(let i=0; i<_this.num_excel_fields; i++) {
              select = $('select[name="option-field-'+i+'"]').val();
              _this.select_import_fields[i] = select;
          }
          if(debug) console.log(_this.select_import_fields, 'select_import_fields: array valori scelti');

          let tmp = '';
          for(let i=0; i<_this.num_excel_fields; i++) {

              if(debug) console.log('tratto select '+i);

              let $select = $('select[name="option-field-'+i+'"]');
              $select.find('option').remove();
              $.each(import_fields, function(key, value) {

                  let exclude_field = false;

                  if(debug) console.log('tratto select '+i+' verifico '+key, 'import_fields');
                  /*
                   * escludo i campi scelti in altri select
                   * tranne IGNORE
                   */
                  for(let ii=0; ii<_this.num_excel_fields; ii++) {

                      if(debug) console.log('tratto campo '+ii+' option '+key, 'escludo i campi scelti in altri select');

                      if(key!='IGNORE' &&
                        _this.select_import_fields[ii]!='' &&
                        _this.select_import_fields[ii]==key &&
                        ii!=i) {
                        exclude_field = true;
                      }
                  }

                  if(!exclude_field) {
                      tmp = '<option value="'+key+'" ';
                      if(_this.select_import_fields[i]==key) tmp += 'selected';
                      tmp += '>'+value+'</option>';
                      $select.append(tmp);
                  }
              });

              _this.setCanImport();

              /*
              * se ho cambiato qualche configurazione delle colonne
              * pulisco la validazione delle ok_step3
              */
             _this.uploadResults = [];
             _this.validazioneResults = [];
             _this.importResult = false;
          }
        },
        setDroppable: async function() {

            return;

            $('#draggable div').draggable({
              helper: 'clone',
              cursor: 'move',
              revert: 'invalid'
            });

          $('#droppable th').droppable({
              accept: '.draggable',
              over: function(event, ui) {
                  // console.log('over');
                  $(this).addClass('ui-state-hover')
              },
              out: function(event, ui) {
                  // console.log('out');
                  $(this).removeClass('ui-state-hover')
              },
              drop: function(event, ui) {
                  let item_orig = $(ui.draggable);
                  $(this)
                    .addClass('ui-state-active')
                    .find('div')
                    .html(item_orig.attr('data-attr-label'));
              }
            });
        },
        setDropzone: async function() {
          var _this = this;

          _this.is_first_row_active = true;

          $('.dropzone').each(function() {
              // ctrl se non e' gia' stato attached
              if(!$(this).hasClass('dz-clickable')) {
                  var myDropzone = new Dropzone(this, {
                    init: function() {
                      this.on('success', function(file, response) {

                        console.log(response, 'upload');

                        _this.file_errors = [];
                        _this.file_contents = [];
                        _this.file_metadatas = [];

                        /*
                         * se carico un nuovo file
                         * pulisco la validazione delle ok_step3
                         */
                        _this.uploadResults = [];
                        _this.validazioneResults = [];
                        _this.importResult = false;

                        _this.is_first_row_active = false;

                        if(response.esito) {
                            _this.file_contents = response.results;
                            /*
                             * prima riga dell'.xlsx e' intestazione
                             * la escludo
                            */
                            if(_this.is_first_row_header)
                              _this.file_contents.shift();  /* remove the first element of the array: */

                            _this.file_metadatas = response.message;

                            setTimeout(() => {
                              _this.setDroppable();
                              _this.num_excel_fields = $('tr#droppable th').length;
                            }, 10);
                        }
                        else {
                          console.log(response.errors, 'upload response.errors');
                          _this.file_errors = response.errors;
                          _this.uploadResults = response.errors;
                        }
                      });
                      this.on('removedfile', function(file) {
                        _this.is_first_row_active = true;
                      });
                      this.on('error', function(file, errorMessage) {
                         console.error(errorMessage, 'errorMessage');
                         if(typeof errorMessage.message !== 'undefined') {
                            _this.file_errors = errorMessage.message;
                            _this.uploadResults.push(errorMessage.message);
                         }
                      });
                    }
                  });
              }
          });
        },
        setCanImport: function() {
          let num_fields_config = 0;
          let select = '';
          // abilito tasto se ogni colonna dell'xsls e configurato
          for(let i=1; i<=this.num_excel_fields; i++) {
            select = $('select[name="option-field-'+i+'"]').val();
            if(select!='') // non ho scelto la configurazione
              num_fields_config++;
          }
          // console.log('num_fields_config '+num_fields_config+' num_excel_fields '+this.num_excel_fields, 'setCanImport');

          this.fields_to_config = num_fields_config;
        },
        frmSubmit: function(e) {

          let _this = this;

          if(!_this.ok_step3) {
            alert("Non tutti i parametri sono stati impostati");
            return false;
          }
          /*
          console.log('select_import_fields '+_this.select_import_fields);
          console.log('supplier_organization_id '+_this.supplier_organization_id);
          console.log('full_path '+_this.file_metadatas.full_path);
          console.log('file_contents '+_this.file_contents);
          */
          let params = {
            select_import_fields: _this.select_import_fields,
            supplier_organization_id: _this.supplier_organization_id,
            full_path: _this.full_path,
            file_contents: _this.file_contents,
          };

          _this.uploadResults = [];
          _this.validazioneResults = [];
          _this.importResult = false;

          $.ajax({url: '/admin/api/articles-import/import',
              data: params,
              method: 'POST',
              dataType: 'html',
              cache: false,
              headers: {
                'X-CSRF-Token': csrfToken
              },
              success: function (response) {
                  response = JSON.parse(response);
                  console.log(response, 'import');
                  if (response.esito) {
                    _this.importResult = true;
                  }
                  else {
                    _this.validazioneResults = response.errors;
                    if(response.errors.length==0) {
                      _this.importResult = true;
                    }
                  }

              },
              error: function (e) {
                  console.error(e, 'import');
                  if(typeof e.responseText!=='undefined')
                    console.error(e.responseText.message, 'import');
              },
              complete: function (e) {
                $('html,body').scrollTop(0);
              }
          });
        }
      },
      mounted: function() {
        console.log('mounted articles-import');
        this.is_run = false;
      },
      computed: {
        ok_step1: function () {
          if(this.supplier_organization!=null && this.supplier_organization.owner_articles=='REFERENT')
            return true;
          else
            return false;
        },
        ok_step2: function () {
          if(this.fields_to_config==0 ||
             this.num_excel_fields==0)
             return false;
          else
          if(this.fields_to_config==this.num_excel_fields) {
            return true;
          }
          else
            return false;
        },
        ok_step3: function () {
          if(this.ok_step1 && this.ok_step2)
            return true;
          else
            return false;
        }
      },
      filters: {
        ownerArticlesLabel(code) {
          if(code) {
            switch(code) {
              case "SUPPLIER":
                  code = "dal produttore";
              break;
              case "PACT":
                  code = "dal gestore del patto";
              break;
              case "REFERENT":
                  code = "dal referente del G.A.S.";
              break;
              case "REFERENT-TMP":
                  code = "temporaneamente dal referente del G.A.S.";
              break;
              case "DES":
                  code = "dal titolare D.E.S. del produttore";
              break;
            }
          }
          return code;
        },
        html(text) {
          return text;
        },
        currency(amount) {
          let locale = window.navigator.userLanguage || window.navigator.language;
          const amt = Number(amount);
          return amt && amt.toLocaleString(locale, {maximumFractionDigits:2}) || '0'
        },
        /*
         * formatta l'importo float che arriva dal database
         * da 1000.5678 in 1.000,57
         * da 1000 in 1.000,00
         */
        formatImportToDb: function(number) {
              var decimals = 2;
              var dec_point = ',';
              var thousands_sep = '.';

              // console.log('formatImportToDb BEFORE number '+number);

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
              // console.log('formatImportToDb AFTER number '+number);

              return number;
          },
        formatDate(value) {
          if (value) {
            let locale = window.navigator.userLanguage || window.navigator.language;
            /* console.log(locale); */
            moment.toLocaleString(locale)
            moment.locale(locale);
            return moment(String(value)).format('DD MMMM YYYY')
          }
        },
        counter: function (index) {
            return index+1
        },
        uppercase : function (text) {
          return text.toUpperCase()
        },
        translateField: function(label) {
          switch(label) {
            case 'IGNORE':
              return 'ignora';
            break;
            case 'id':
              return 'Identificativo articolo';
            break;
            case 'name':
              return 'Nome articolo';
            break;
            case 'codice':
              return 'Codice';
            break;
            case 'prezzo':
              return 'Prezzo';
            break;
            case 'qta':
              return 'Quantità';
            break;
            case 'um':
              return 'Unità di misura';
            break;
            case 'pezzi_confezione':
              return 'Pezzi confezione';
            break;
            case 'bio':
              return 'Biologico';
            break;
            case 'ingredienti':
              return 'Ingredienti';
            break;
            case 'note':
              return 'Nota';
            break;
            case 'nota':
              return 'Nota';
            break;
            case 'qta_minima':
              return 'Quantità minima che un gasista può acquistare';
              break;
            case 'qta_minima_order':
              return 'Quantità minima rispetto a tutti gli acquisti';
              break;
            case 'qta_multipli':
              return 'Multipli di';
            break;
            case 'flag_presente_articlesorders':
              return 'Ordinabili';
            break;
            case 'qta_um':
              return 'Quantità e Unità di misura';
            break;
            default:
              return label;
            break;
          }
        }
      }
    });
});
