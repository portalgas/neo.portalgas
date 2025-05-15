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
            errors: {},
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
              if(this.article.bio=='Y') {
                  this.article.bio = 'N';
                  if(this.article.articles_types_ids.includes("1"))
                      this.article.articles_types_ids = this.article.articles_types_ids.filter(elemento => elemento !== "1");
              }
              else {
                  this.article.bio = 'Y';
                  if(!this.article.articles_types_ids.includes("1"))
                      this.article.articles_types_ids.push("1");
              }
          },
          toggleTypes: function() {
               console.log(this.article.articles_types_ids, 'toggleTypes');
               if(this.article.articles_types_ids.includes("1"))
                   this.article.bio = 'Y';
               else
                   this.article.bio = 'N';
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
          /*
           * setta
           *    um riferimento
           *    prezzo finale
           */
          changeArticleVariant: function(event, index) {
              let um = this.article_variants[index].um;
              let prezzo = numberToJs(this.article_variants[index].prezzo);
              let qta = numberToJs(this.article_variants[index].qta);

              console.log('changeUM index '+index+' um '+um+" prezzo "+prezzo+" qta "+qta);
              if(prezzo==0 || qta==0 || um=='') {
                  this.article_variants[index].um_rif_values = [];
                  return;
              }

              let prezzo_um_riferimento = (prezzo / qta);
              console.log(prezzo_um_riferimento, 'changeUM prezzo_um_riferimento');

              let prezzo_finale = 0;
              if(this.article_variants[index].iva!='inclusa') {
                  let iva = this.article_variants[index].iva;
                  let delta_iva = (prezzo/100)*iva;
                  prezzo_finale = (parseFloat(prezzo) + parseFloat(delta_iva));
              }
              else
                  prezzo_finale = prezzo;
              this.article_variants[index].prezzo_finale = numberFormat(prezzo_finale,2,',','.');;

              this.article_variants[index].um_rif_values = getUmRifValues(um, prezzo_um_riferimento);
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
          setValidate: function(event) {
                let _this = this;
                console.log(event);
                console.log('setValidate id ['+event.target.id+'] name ['+event.target.name+'] value ['+event.target.value+']');

                this.validate(event.target.name);
          },
          validate(field_name) {
              // console.log('validate field_name ['+field_name+']: value ['+this.article[field_name]+']');
              if(typeof this.article[field_name]==='undefined' || this.article[field_name]=='') {
                  let msg = '';
                  switch (field_name) {
                      case 'name':
                          msg =  "Indica il nome dell'articolo";
                          break;
                      case 'supplier_organization_id':
                          msg =  "Scegli il produttore";
                          break;
                      default:
                          msg =  "Campo obbligatorio";
                  }
                  // this.errors[field_name] = msg; workaround perche' non era reattivo
                  this.$set(this.errors, field_name, msg);
              }
              else {
                  // delete this.errors[field_name]; workaround perche' non era reattivo
                  this.$delete(this.errors, field_name);
              }
          },
          frmSubmit: function(e) {
            let _this = this;

            this.validate('name');
            this.validate('supplier_organization_id');
            if(Object.keys(_this.errors).length>0)
                return false;

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

          numberToJs('1000,50');

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
