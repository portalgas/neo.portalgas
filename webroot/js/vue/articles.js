"use strict";

var articles = null;

$(function () {

    var router = new VueRouter({
                mode: 'history',
                routes: []
            });

    articles = new Vue({
      router,
      el: '#vue-articles',
      data: {
        open_box_price: false,
        iva: 10,
        ums: ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'],
        search_supplier_organization_id: '',
        search_name: '',
        search_codice: '',
        search_categories_articles: [],
        search_categories_article_id: 0,
        search_id: null,
        search_flag_presente_articlesorders: 'Y',
        search_order: 'Articles.name ASC',
        article_in_carts: [],
        articles: [],
        is_run: false,
        is_run_categories_articles: false,
        is_run_paginator: false,
        list_articles_finish: false,
        page: 1,
        isScrollFinish: false,
        autocomplete_field: '',
        autocomplete_name_results: [],
        autocomplete_name_is_open: false,
        autocomplete_name_is_loading : false,
        autocomplete_name_arrow_counter: -1,
        autocomplete_name_items: [],
        autocomplete_codice_results: [],
        autocomplete_codice_is_open: false,
        autocomplete_codice_is_loading : false,
        autocomplete_codice_arrow_counter: -1,
        autocomplete_codice_items: [],
      },
      methods: {
        /*
         * autocomplete
         */
        handleClickOutsideAutocomplete: function(event) {
          let id = event.target.id;
          // if (!this.$el.contains($search_codice)) {
          if (id!='search-name') {
            this.autocomplete_name_arrow_counter = -1;
            this.autocomplete_name_is_open = false;
          }
          if (id!='search-codice') {
            this.autocomplete_codice_arrow_counter = -1;
            this.autocomplete_codice_is_open = false;
          }
        },
        onArrowDownSearchName: function() {
          if (this.autocomplete_name_arrow_counter < this.autocomplete_name_results.length) {
            this.autocomplete_name_arrow_counter = this.autocomplete_name_arrow_counter + 1;
          }
        },
        onArrowDownSearchCodice: function() {
          if (this.autocomplete_codice_arrow_counter < this.autocomplete_codice_results.length) {
            this.autocomplete_codice_arrow_counter = this.autocomplete_codice_arrow_counter + 1;
          }
        },
        onArrowUpSearchName: function() {
          if (this.autocomplete_name_arrow_counter > 0) {
            this.autocomplete_name_arrow_counter = this.autocomplete_name_arrow_counter - 1;
          }
        },
        onArrowUpSearchCodice: function() {
          if (this.autocomplete_codice_arrow_counter > 0) {
            this.autocomplete_codice_arrow_counter = this.autocomplete_codice_arrow_counter - 1;
          }
        },
        filterSearchAutoCompleteResults: function(autocomplete_field) {
          // console.log('filterSearchAutoCompleteResults autocomplete_field '+autocomplete_field);
          if(autocomplete_field=='name')
            this.autocomplete_name_results = this.autocomplete_name_items.filter(item => item.toLowerCase().indexOf(this.search_name.toLowerCase()) > -1);
          else
          if(autocomplete_field=='codice')
            this.autocomplete_codice_results = this.autocomplete_codice_items.filter(item => item.toLowerCase().indexOf(this.search_codice.toLowerCase()) > -1);
        },
        onChangeSearchAutoComplete: function(autocomplete_field) {

              let _this = this;
              _this.autocomplete_field = autocomplete_field;

              // console.log('onChangeSearchAutoComplete autocomplete_field '+autocomplete_field);

              this.autocomplete_name_is_loading = true;
              let params = {}
              if(autocomplete_field=='name') {
                if(this.search_name.length<=3)
                  return;

                params = {
                  search_supplier_organization_id: this.search_supplier_organization_id,
                  search_name: this.search_name,
                  field: 'name'
                };
              }
              else
              if(autocomplete_field=='codice') {
                if(this.search_codice.length<=3)
                  return;

                params = {
                  search_supplier_organization_id: this.search_supplier_organization_id,
                  search_codice: this.search_codice,
                  field: 'codice'
                };
              }

              axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
              axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;

              axios.post('/admin/api/articles/getAutocomplete', params)
                  .then(response => {
                    // console.log(response.data, 'get');

                    this.autocomplete_name_is_loading = false;

                    if(response.data.code=='200') {
                      if(response.data.results.length==0) {
                        this.autocomplete_name_is_open = false;
                      }
                      else {
                        if(_this.autocomplete_field=='name') {
                          this.autocomplete_name_items = response.data.results;
                          this.filterSearchAutoCompleteResults(_this.autocomplete_field);
                          this.autocomplete_name_is_open = true;
                        }
                        else
                        if(_this.autocomplete_field=='codice') {
                          this.autocomplete_codice_items = response.data.results;
                          this.filterSearchAutoCompleteResults(_this.autocomplete_field);
                          this.autocomplete_codice_is_open = true;
                        }
                      }
                    }
                    else {
                      console.error(response.data.errors);
                    }
                  })
              .catch(error => {
                this.autocomplete_name_is_loading = false;
                console.error("Error: " + error);
              });
        },
        /*
         * scelto un suggerimento di autocpmplete 
         */
        setSearchAutoCompleteResult(result, field) {
          if(field=='name') {
            this.search_name = result;
            this.autocomplete_name_is_open = false;
          }
          else
          if(field=='codice') {
            this.search_codice = result;
            this.autocomplete_codice_is_open = false;
          }
          this.gets();
        },
        /*
         * autocomplete end
         */
        changeUM: function(event, index) {

            // console.log(event.target, 'changeUM');
            // console.log('changeUM index '+index+' id '+event.target.id+' name '+event.target.name+' value '+event.target.value);
          let um = event.target.value;
          let prezzo = this.articles[index].prezzo_;
          prezzo = numberToJs(prezzo);
          let qta = this.articles[index].qta;
          qta = numberToJs(qta);
          let prezzo_um_riferimento = (prezzo / qta);
            // console.log(prezzo_um_riferimento, 'changeUM prezzo_um_riferimento');

          this.articles[index].um_rif_values = getUmRifValues(um, prezzo_um_riferimento);
        },
        openBoxPrice: function() {
          this.open_box_price = !this.open_box_price;
        },
        setPriceConIva: function(index) {

          let iva = this.iva;
            // console.log(iva, 'changePrice iva');

          let prezzo_no_iva = $('#prezzo_no_iva-'+this.articles[index].organization_id+'-'+this.articles[index].id).val();

          if(iva!=0 && prezzo_no_iva!=='') {
            prezzo_no_iva = numberToJs(prezzo_no_iva);
            let delta_iva = (prezzo_no_iva/100)*iva;
            // console.log("delta_iva "+delta_iva);
            //delta_iva = Math.round(delta_iva);
            //console.log("Math.round(delta_iva) "+delta_iva);

            let prezzo = (parseFloat(prezzo_no_iva) + parseFloat(delta_iva));
            let prezzo_ = numberFormat(prezzo,2,',','.');
            this.articles[index].prezzo = prezzo;
            this.articles[index].prezzo_ = prezzo_;

            let field_id = 'prezzo-'+this.articles[index].organization_id+'-'+this.articles[index].id;
            let field_name = 'prezzo';
            let field_value = prezzo_;
            this.setValue(field_id, field_name, field_value, index);
          }
        },
        changeValue: function(event, index) {
          console.log(event.target, 'changeValue');
          console.log('changeValue index ['+index+'] id ['+event.target.id+'] name ['+event.target.name+'] value ['+event.target.value+']');

          let field_id = event.target.id;  // um_rif_values-DL
          let field_name = event.target.name; // um_riferimento
          let field_value = event.target.value; // DL

          this.setValue(field_id, field_name, field_value, index);
        },
        setValue: function(field_id, field_name, field_value, index) {

          let field = $('#'+field_id);
          // console.log(field, 'changeValue field');
          // console.log(field.parent(), 'changeValue field.parent()');
          field.parent().append('<div id="esito-'+field_id+'"></div>');
          let $responseHtml = $('#esito-'+field_id);
          $responseHtml.addClass('fa-lg fa fa-spinner');

          if(!this.articles[index].can_edit) {
            alert("L'articolo non è modificabile perchè gestito da <b>"+this.articles[index].organization.name+'</b>');
            return;
          }

          /*
           * passo l'array completo delle tipologie
           */
          if(field_name=='article_type_ids') {
              field_value = this.articles[index].articles_types
          }

          let params = {
              id: this.articles[index].id,
              organization_id: this.articles[index].organization_id,
              name: field_name,
              value: field_value
          };
          // console.log(params, 'setValue params');

          axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
          axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;

          axios.post('/admin/api/articles/setValue', params)
              .then(response => {
                  // console.log(response.data, 'set');

                $responseHtml.removeClass('fa-lg fa fa-spinner');

                if(response.data.code=='200') {
                  $responseHtml.addClass('fa-lg text-green fa fa-thumbs-o-up');
                }
                else {
                  $responseHtml.addClass('fa-lg text-red fa fa-thumbs-o-down');
                  console.error(response.data.errors);
                  alert(response.data.errors);

                  if(field_name=='stato' && field_value=='N') {
                    this.articles[index].stato = 'Y';
                  }
                }

                setTimeout( function() {$responseHtml.remove()} , 2500);
              })
          .catch(error => {
            console.error("Error: " + error);
          });
        },
        goToDelete: function(index) {
          let article_organization_id = this.articles[index].organization_id;
          let article_id = this.articles[index].id;
          let url = portalgas_bo_url+'/administrator/index.php?option=com_cake&controller=Articles&action=context_articles_delete&id='+article_id+'&article_organization_id='+article_organization_id;
          window.location.href = url;
        },
        goToCopy: function(index) {
            if(!confirm('Sei sicuro di voler copiare l\'articolo?'))
                return false;

              let article_organization_id = this.articles[index].organization_id;
              let article_id = this.articles[index].id;
              let url = '/admin/articles/copy/'+article_organization_id+'/'+article_id;
              window.location.href = url;
        },
          toggleArticleTypes: function(index) {
              // console.log(this.articles[index].articles_types, 'toggleArticleTypes');
              let is_bio = this.articles[index].articles_types.indexOf(1);
              if (is_bio > -1) {
                  this.articles[index].bio = 'Y';
              }
              else
                  this.articles[index].bio = 'N';
          },
        toggleIsBio: function(field_id, index) {
          // console.log(this.articles[index].bio, 'toggleIsBio');
          if(this.articles[index].bio=='Y') {
              this.articles[index].bio = 'N';

              // elimino article_type = 1 BIO
              let indexToRemove = this.articles[index].articles_types.indexOf(1);
              if (indexToRemove > -1) {
                  this.articles[index].articles_types.splice(indexToRemove, 1);
              }
          }
          else {
              this.articles[index].bio = 'Y';
              // aggiungi article_type = 1 BIO
              this.articles[index].articles_types.push(1);
          }

          // console.log(field_id, 'field_id');
          let field_name = 'bio';
          let field_value = this.articles[index].bio;

          this.setValue(field_id, field_name, field_value, index);
        },
        toggleFlagPresenteArticlesOrders: function(field_id, index) {
          // console.log(this.articles[index].flag_presente_articlesorders, 'toggleFlagPresenteArticlesOrders');
          if(this.articles[index].flag_presente_articlesorders=='Y')
              this.articles[index].flag_presente_articlesorders = 'N';
          else
              this.articles[index].flag_presente_articlesorders = 'Y';

            // console.log(field_id, 'field_id');
          let field_name = 'flag_presente_articlesorders';
          let field_value = this.articles[index].flag_presente_articlesorders;

          this.setValue(field_id, field_name, field_value, index);
        },
        toggleStato: function(field_id, index) {
            // console.log(this.articles[index].stato, 'toggleStato');
          if(this.articles[index].stato=='Y')
              this.articles[index].stato = 'N';
          else
              this.articles[index].stato = 'Y';

            // console.log(field_id, 'field_id');
          let field_name = 'stato';
          let field_value = this.articles[index].stato;

          this.setValue(field_id, field_name, field_value, index);
        },
        toggleSearchFlagPresenteArticlesOrders: function() {
          this.search_flag_presente_articlesorders = !this.search_flag_presente_articlesorders;
        },
        toggleExtra: function(index) {
            // console.log('.extra-'+index, 'toggleExtra');
          $('.extra-'+index).toggle('slow');
        },
        modalInCarts: function(index) {

          this.article_in_carts = [];

          let params = {
            article_organization_id: this.articles[index].organization_id,
            article_id: this.articles[index].id
          };
            // console.log(params, 'modalInCarts');

          axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
          axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;

          axios.post('/admin/api/articles/getInCarts', params)
              .then(response => {
                  // console.log(response.data, 'getInCarts');

                if(response.data.code=='200') {
                  $('#modalArticleInCarts').modal('show');
                  this.article_in_carts = response.data.results;
                }
              })
          .catch(error => {
            console.error("Error: " + error);
          });
        },
        gets: async function() {

          this.articles = [];
          this.page = 1;
          this.list_articles_finish = false;

          await this.scroll();
          this.isScrollFinish = false;
        },
        scroll: async function() {
          // console.log('scroll page '+this.page+' is_run_paginator '+this.is_run_paginator+' isScrollFinish '+this.isScrollFinish);
          if(this.isScrollFinish || this.is_run_paginator || this.list_articles_finish)
            return;

          if (this.page==1) {
            this.is_run = true;
            await this.getArticles();
            await setTimeout(() => {
               this.setDropzone();
            }, 10);
            this.is_run = false;
          }

          window.onscroll = async () => {
            let scrollTop = Math.floor(document.documentElement.scrollTop);
            let bottomOfWindow = scrollTop + window.innerHeight > (document.documentElement.offsetHeight - 10);
            // console.log((scrollTop + window.innerHeight)+' '+(document.documentElement.offsetHeight - 10));

            /*
            scrollTop    height to top
            innerHeight  height windows
            offsetHeight height page
            console.log('document.documentElement.scrollTop '+scrollTop);
            console.log('window.innerHeight '+window.innerHeight);
            console.log('document.documentElement.offsetHeight '+document.documentElement.offsetHeight);
            console.log('bottomOfWindow '+bottomOfWindow);
            */

            if (bottomOfWindow && !this.is_run_paginator && !this.isScrollFinish && !this.list_articles_finish) {
                await this.getArticles();
                await this.setDropzone();
            }
          };
        },
        changeSearchSupplierOrganizationId: function() {

          this.search_supplier_organization_id = $('#search_supplier_organization_id').val();
          this.getCategoriesArticles();
          this.gets();
        },
        /*
         * estraggo le categorie degli articoli
         * se il produttore gestisce il listino prendo quelle del produttore
         */
        getCategoriesArticles: function() {

          this.is_run_categories_articles = true;

          this.search_categories_articles = [];

          let params = {
            search_supplier_organization_id: this.search_supplier_organization_id,
          };
          // console.log(params, 'getCategoriesArticles params');

          axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
          axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;

          axios.post('/admin/api/categories-articles/gets', params)
              .then(response => {
                // console.log(response.data, 'categories-articles gets');

                this.is_run_categories_articles = false;

                if(response.data.code=='200') {
                  this.search_categories_articles = response.data.results;
                }
                else {
                  this.is_run_categories_articles = false;
                  console.error(response.data.errors);
                }
              })
          .catch(error => {
            console.error("Error: " + error);
          });

        },
        getArticles: async function() {

          this.is_run_paginator = true;

          // workaround per la class select2
          let search_categories_article_id = $('select[name=search_categories_article_id]').val();
          this.search_categories_article_id = search_categories_article_id;
    
          /*
           * se scelgo un campo di ricerca non considero search_id
           */
          if(this.search_supplier_organization_id!='' || this.search_name!='' || this.search_codice!='')
              search_id = null;

          let params = {
              search_supplier_organization_id: this.search_supplier_organization_id,
              search_name: this.search_name,
              search_codice: this.search_codice,
              search_categories_article_id: search_categories_article_id,
              search_id: search_id, // definito in index_quick.ctp
              search_flag_presente_articlesorders: this.search_flag_presente_articlesorders,
              search_order: this.search_order,
              page: this.page
          };
    
          axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
          axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;

          await axios.post('/admin/api/articles/gets', params)
              .then(response => {
                  // console.log(response.data, 'gets');

                this.is_run_paginator = false;
                this.isScrollFinish = false;

                if(response.data.code=='200') {

                  var data = response.data.results;
                  for (var i = 0; i < data.length; i++) {
                      this.articles.push(data[i]);
                  }
                  this.page++;
                }
                else {
                  console.error(response.data.errors);
                }
              })
          .catch(error => {
            this.is_run_paginator = false;
            this.isScrollFinish = true;
            console.error("Error: " + error);
          });
        },
        setDropzone: async function() {
          let _this = this;

          // console.log($('.dropzone').length, 'dropzone.length');
          // console.log(csrfToken, 'csrfToken');
          $('.dropzone').each(function() {
              // ctrl se non e' gia' stato attached
              if(!$(this).hasClass('dz-clickable')) {

                  let index = $(this).attr('data-attr-index');
                  // console.log(index, 'dropzone data-attr-index');

                  var myDropzone = new Dropzone(this, {
                        url: '/admin/api/articles/img1Upload/'+_this.articles[index]['organization_id']+'/'+_this.articles[index]['id'],
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        beforeSend: function(request) {
                          // console.log(csrfToken, 'csrfToken');
                            return request.setRequestHeader('X-CSRF-Token', csrfToken);
                        },
                        dictDefaultMessage: 'Trascina qui la foto dell\'articolo',
                        dictRemoveFile: 'Elimina foto',
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
                          // ctrl size perche' se c'e' il placeholder /img/article-no-img.png
                          if(_this.articles[index]['img1_size']>0) {

                              // console.log(index + ' ' + _this.articles[index]['img1_size'] + ' ' + _this.articles[index]['img1'], 'dropzone');

                              let path = _this.articles[index]['img1'];

                              let myDropzone = this;
                              let mockFile = { name: 'Foto articolo', size: _this.articles[index]['img1_size'], accepted: true};
                              // Uncaught DOMException: The operation is insecure.
                              // myDropzone.displayExistingFile(mockFile, path);
                              // myDropzone.createThumbnailFromUrl(mockFile, path);

                              myDropzone.emit("addedfile", mockFile);
                              myDropzone.emit("thumbnail", mockFile, path);
                              myDropzone.emit("success", mockFile);
                              myDropzone.emit("complete", mockFile);
                              this.files.push(mockFile);
                          }

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
                            // console.log(response, 'success response');
                          });
                          this.on('removedfile', function(file) {
                              // console.log(file, 'removedfile');
                              $.ajax({
                                url: '/admin/api/articles/img1Delete/'+_this.articles[index]['organization_id']+'/'+_this.articles[index]['id'],
                                type: 'post',
                                headers: {
                                  'X-CSRF-TOKEN': csrfToken
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
                    }); // new Dropzone

                    myDropzone.on('processing', function (file) {
                      // console.log('dropzone processing');
                    });
                    myDropzone.on('queuecomplete', function (file) {
                      // console.log('dropzone queuecomplete');
                    });
              } // if(!$(this).hasClass('dz-started'))
            }); // $('.dropzone').each(function()
        },
      },
      /*
      watch: {
        'articles': {
          handler (newValue, oldValue) {
            newValue.forEach((article) => {
              console.log(article, 'watch');
            })
          },
          deep: true
        }
      },*/
      mounted: function() {

        let _this = this;

          // console.log('mounted articles');
        /*
         * se l'elenco dei produttori ha un solo elemente (ex produttore) lo imposto gia'
         */
        if(typeof search_supplier_organization_id_default!=='undefined')
          this.search_supplier_organization_id = search_supplier_organization_id_default;
        this.gets();
        document.addEventListener('click', _this.handleClickOutsideAutocomplete);

        $('.select2').on('change', function (e) { _this.changeSearchSupplierOrganizationId() });

        this.getCategoriesArticles();
      },
      destroyed() {
        document.removeEventListener('click', this.handleClickOutsideAutocomplete);
      },
      filters: {
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
        }
      }
    });
});
