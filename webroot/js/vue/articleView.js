"use strict";

var articleView = null;

$(function () {

    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    articleView = new Vue({
        router,
        el: '#vue-articles-view',
        data: {
        article: {},
        article_variants: [],
        ums: ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'],
        ivas: ['inclusa', 4, 10, 22],
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
        is_run: true,
      },
      methods: {
          init: function() {
              this.article_variants = []
          },
          get: function() {
              let _this = this;
              $.ajax({
                  url: '/admin/api/articles/get/'+article_organization_id+'/'+article_id,
                  method: 'GET',
                  dataType: 'json',
                  cache: false,
                  headers: {
                      'X-CSRF-Token': csrfToken
                  },
                  success: async function (response) {
                      console.log(response, 'get');
                      if (response.code == 200) {
                          _this.article = response.results.article;
                          if(response.results.article_variants.length>0)
                              response.results.article_variants.forEach(function (article_variant) {
                                  _this.article_variants.push(article_variant);
                              });
                      }
                  },
                  error: function (e) {
                      console.error(e, 'get');
                      if (typeof e.responseText !== 'undefined')
                          console.error(e.responseText.message, 'get');
                  },
                  complete: function (e) {
                  }
              });
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
          addRow() {
              let new_article_variant = {
                  id: null,
                  parent_id: null,
                  codice: '',
                  qta: 1,
                  um: 'PZ',
                  prezzo: '0,00',
                  iva: 'inclusa',
                  prezzo_finale: '0,00',
                  um_riferimento: 'PZ',
                  pezzi_confezione: 1,
                  qta_minima: 1,
                  qta_massima: 0,
                  qta_minima_order: 0,
                  qta_multipli: 1,
                  qta_massima_order: 0,
                  stato: 'Y',
                  flag_presente_articlesorders:  'Y',
                  um_rif_values: [],
              };
              this.article_variants.push(new_article_variant);
          },
          removeRow: function(index) {
              this.article_variants.splice(index, 1);
          },
          toggleIsBio: function() {
              // console.log(this.article.bio, 'toggleIsBio');
              if(this.article.bio=='Y')
                  this.article.bio = 'N';
              else
                  this.article.bio = 'Y';
          },
          toggleFlagPresenteArticlesOrders: function(index) {
              // console.log(this.article_variants[index].flag_presente_articlesorders, 'toggleFlagPresenteArticlesOrders');
              if(this.article_variants[index].flag_presente_articlesorders=='Y')
                  this.article_variants[index].flag_presente_articlesorders = 'N';
              else
                  this.article_variants[index].flag_presente_articlesorders = 'Y';
          },
          toggleStato: function(index) {
              // console.log(this.article_variants[index].stato, 'toggleStato');
              if(this.article_variants[index].stato=='Y')
                  this.article_variants[index].stato = 'N';
              else
                  this.article_variants[index].stato = 'Y';
          },
          setPrezzoFinale: function(event, index) {
              console.log(event.target, 'setPrezzoFinale');
              console.log('setPrezzoFinale index ['+index+'] id ['+event.target.id+'] name ['+event.target.name+'] value ['+event.target.value+']');

              if(this.article_variants[index].iva!='inclusa')
                  this.article_variants[index].prezzo_finale = (this.article_variants[index].prezzo + (this.article_variants[index].prezzo * this.article_variants[index].iva) / 100);
              else
                  this.article_variants[index].prezzo_finale = this.article_variants[index].prezzo;
          },
          /*
          * valore in 1000.50
          */
          numberToJs: function(number) {

              if(typeof number==='undefined' || number=='undefined') return '0.00';

              /* elimino le migliaia */
              number = number.replace('.','');

              /* converto eventuali decimanali */
              number = number.replace(',','.');

              return number;
          },
          /*
            da 1000.5678 in 1.000,57
            da 1000 in 1.000,00
          */
          numberFormat: function(number, decimals, dec_point, thousands_sep) {

              var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
              var d = dec_point == undefined ? "." : dec_point;
              var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
              var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

              return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
          },
          changeUM: function(event, index) {

              console.log('changeUM index '+index+' value '+event.target.value);

              let um = event.target.value;
              let prezzo = this.article_variants[index].prezzo_finale;
             // prezzo = this.numberToJs(prezzo);
              let qta = this.article_variants[index].qta;
             // qta = this.numberToJs(qta);
              let prezzo_um_riferimento = (prezzo / qta);
              console.log(prezzo_um_riferimento, 'changeUM prezzo_um_riferimento');

              let um_rif_values = [];
              let um_rif_values_prezzo = 0;
              if (um == 'PZ') {
                  um_rif_values_prezzo = prezzo_um_riferimento;
                  um_rif_values.push({id: 'PZ', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;'});
              } else
              if (um == 'KG') {
                  um_rif_values_prezzo = (prezzo_um_riferimento / 1000);
                  um_rif_values.push({id: 'GR', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento / 10);
                  um_rif_values.push({id: 'HG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'KG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
              } else
              if (um == 'HG') {
                  um_rif_values_prezzo = (prezzo_um_riferimento / 100);
                  um_rif_values.push({id: 'GR', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'HG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 10);
                  um_rif_values.push({id: 'KG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
              } else
              if (um == 'GR') {
                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'GR', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 100);
                  um_rif_values.push({id: 'HG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 1000);
                  um_rif_values.push({id: 'KG', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
              } else
              if (um == 'LT') {
                  um_rif_values_prezzo = (prezzo_um_riferimento / 1000);
                  um_rif_values.push({id: 'ML', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento / 10);
                  um_rif_values.push({id: 'DL', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'LT', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
              } else
              if (um == 'DL') {
                  um_rif_values_prezzo = (prezzo_um_riferimento / 100);
                  um_rif_values.push({id: 'ML', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'DL', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 10);
                  um_rif_values.push({id: 'LT', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
              } else
              if (um == 'ML') {
                  um_rif_values_prezzo = (prezzo_um_riferimento);
                  um_rif_values.push({id: 'ML', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 100);
                  um_rif_values.push({id: 'DL', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

                  um_rif_values_prezzo = (prezzo_um_riferimento * 1000);
                  um_rif_values.push({id: 'LT', value: this.numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
              }

              this.article_variants[index].um_rif_values = um_rif_values;
          },
          setDropzone: async function() {
              let _this = this;
              new Dropzone('div#myDropzoneImage', {
                  url: '/admin/api/articles/img1Upload/'+article_organization_id+'/'+article_id,
                  headers: {
                      'X-CSRF-TOKEN': csrfToken
                  },
                  beforeSend: function(request) {
                      // console.log(csrfToken, 'csrfToken');
                      return request.setRequestHeader('X-CSRF-Token', csrfToken);
                  },
                  dictDefaultMessage: "Trascina qui l'immagine",
                  dictRemoveFile: 'Elimina immagine',
                  dictFallbackMessage: 'Il tuo browser non supporta il drag\'n\'drop dei file.',
                  dictFallbackText: 'Please use the fallback form below to upload your files like in the olden days.',
                  dictFileTooBig: 'Il file è troppo grande ({{filesize}}MiB). Grande massima consentita: {{maxFilesize}}MiB.',
                  dictInvalidFileType: 'Non puoi uploadare file di questo tipo.',
                  dictResponseError: 'Server responded with {{statusCode}} code.',
                  dictCancelUpload: 'Cancel upload',
                  dictCancelUploadConfirmation: 'Are you sure you want to cancel this upload?',
                  dictMaxFilesExceeded: 'Non puoi uploadare più file.',
                  parallelUploads: 1,
                  addRemoveLinks: true,
                  uploadMultiple:false,
                  maxFiles: 1,
                  // resizeWidth: 175,
                  // acceptedFiles: 'image/*',
                  acceptedFiles: '.jpeg,.jpg,.png,.gif,.webp',
                  paramName: 'img1', // The name that will be used to transfer the file
                  maxFilesize: 5, // MB
                  init: function() {
                      this.on('addedfile', function(file) {
                          // console.log('addedfile - this.files.length '+this.files.length);
                          if (this.files.length > 1) {
                              this.removeFile(this.files[0]);
                          }
                      });
                      this.on('maxfilesexceeded', function(file) {
                          // console.log('maxfilesexceeded');
                          this.removeAllFiles();
                          this.addFile(file);
                      });
                      this.on('success', function(file, response) {
                          if(response.esito) {

                          }
                          console.log(response, 'success response');
                          _this.article.img1 = response.message.file_name;
                          // _this.article.img1_full_path = response.message.full_path;
                      });
                      this.on('removedfile', function(file) {
                          // console.log(file, 'removedfile');
                          $.ajax({
                              url: '/admin/api/cms-images/img1Delete/',
                              type: 'post',
                              headers: {
                                  'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')
                              }
                          });
                      });
                  },
                  accept: function(file, done) {
                      if (file.name == 'justinbieber.jpg') {
                          done('dropzone eseguito');
                      }
                      else { done(); }
                  }
              });
          },
          frmSubmit: function(e) {

            let _this = this;

            let params = {
                article: _this.article,
                article_variants: _this.article_variants
            };

            _this.is_run = true;

          $.ajax({url: '/admin/api/articles/store',
              data: params,
              method: 'POST',
              dataType: 'html',
              cache: false,
              headers: {
                'X-CSRF-Token': csrfToken
              },
              success: function (response) {
                  console.log(response, 'store');
                  if (response.esito) {
                      _this.is_run = false;
                  }
                  else {
                    _this.validazioneResults = response.errors;
                    if(response.errors.length==0) {
                        _this.is_run = false;
                    }
                  }

              },
              error: function (e) {
                  console.error(e, 'store');
                  if(typeof e.responseText!=='undefined')
                    console.error(e.responseText.message, 'store');
              },
              complete: function (e) {
                  _this.is_run = false;
              }
          });
        }
      },
      mounted: function() {
        console.log('mounted articles-view');
        this.is_run = false;
        this.get();
        this.setDropzone();
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
        }
      }
    });
});
