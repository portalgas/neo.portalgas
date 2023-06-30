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
        ums: ['PZ', 'GR', 'HG', 'KG', 'ML', 'DL', 'LT'],
        search_supplier_organization_id: '',
        search_name: '',
        search_codice: '',
        articles: [],
        is_run: false
      },  
      methods: {
        toggleIsBio: function(index) {
          console.log(this.articles[index].bio, 'toggleIsBio');
          if(this.articles[index].bio=='Y')
            this.articles[index].bio = 'N';
          else 
            this.articles[index].bio = 'Y';
        },
        toggleFlagPresenteArticlesOrders: function(index) {
          console.log(this.articles[index].flag_presente_articlesorders, 'toggleFlagPresenteArticlesOrders');
          if(this.articles[index].flag_presente_articlesorders=='Y')
            this.articles[index].flag_presente_articlesorders = 'N';
          else 
            this.articles[index].flag_presente_articlesorders = 'Y';
        },
        toggleExtra: function(index) {
          console.log('.extra-'+index, 'toggleExtra');
          $('.extra-'+index).toggle('slow');
        },
        gets: async function() {
          await this.getArticles();
          await this.setDropzone();
        },
        getArticles: async function() {
          this.is_run = true;

          let params = {
              search_supplier_organization_id: this.search_supplier_organization_id,
              search_name: this.search_name,
              search_codice: this.search_codice
          }; 
          console.log(params, 'getArticles params'); 

          axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
          axios.defaults.headers.common['X-CSRF-Token'] = csrfToken;  

          await axios.post('/admin/api/articles/gets', params)
              .then(response => {
                console.log(response.data, 'gets'); 
                
                this.is_run = false;
                if(response.data.code=='200') {
                  this.articles = response.data.results; 
                }
                else {
                  console.error(response.data.errors);
                }
              })
          .catch(error => {
            this.is_run = false;
            console.error("Error: " + error);
          });            
        },
        setDropzone: function() {
          let _this = this;

          $('.dropzone').each(function() {
                
              let index = $(this).attr('data-attr-index');
              console.log(index, 'data-attr-index');

              var myDropzone = new Dropzone(this, {
                    url: '/admin/articles/img1/upload/'+_this.articles[index]['organization_id']+'/'+_this.articles[index]['id'], 
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
                    acceptedFiles: '.jpeg,.jpg,.png,.gif',
                    paramName: 'img1', // The name that will be used to transfer the file
                    maxFilesize: 5, // MB  
                    init: function() {

                      // ctrl size perche' se c'e' il placeholder /img/article-no-img.png
                      if(_this.articles[index]['img1_size']>0) {
                          let myDropzone = this;
                          let mockFile = { name: 'Foto articolo', size: _this.articles[index]['img1_size']};
                          myDropzone.displayExistingFile(mockFile, _this.articles[index]['img1']);
                          this.files.push(mockFile);
                      }

                      this.on('addedfile', function(file) {
                        console.log('addedfile - this.files.length '+this.files.length);
                        if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                        }
                      });		
                      this.on('maxfilesexceeded', function(file) {
                              console.log('maxfilesexceeded');
                        this.removeAllFiles();
                              this.addFile(file);
                          });
                      this.on('success', function(file, response) {
                        if(response.esito) {
                  
                        }
                        console.log(response, 'success response'); 
                  
                      });		
                      this.on('removedfile', function(file) {
                        console.log(file, 'removedfile'); 
                        $.post('/admin/articles/img1/delete/'+_this.articles[index]['organization_id']+'/'+_this.articles[index]['id']); 
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
                  console.log('dropzone processing');
                });
                myDropzone.on('queuecomplete', function (file) {
                    console.log('dropzone queuecomplete');
                });                

            }); // $('.dropzone').each(function()
        },
        um_label: function(index) {

          let prezzo = this.articles[index].prezzo;
          let qta = this.articles[index].prezzo;
          let um_riferimento = this.articles[index].um_riferimento;
          let prezzo_um_riferimento = (prezzo / qta);
          
          return prezzo_um_riferimento + ' al ' + um_riferimento;
          /*
          var str = '';
              if (um == 'PZ') {
                  str += '<input class="nospace" checked="checked" type="radio" value="PZ" id="ArticleUmRiferimentoPZ" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoPZ">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Pezzo</label>';
              } else
              if (um == 'KG') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'GR') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="GR" id="ArticleUmRiferimentoGR" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoGR">' + number_format(prezzo_um_riferimento / 1000, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'HG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="HG" id="ArticleUmRiferimentoHG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoHG">' + number_format(prezzo_um_riferimento / 100, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'KG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="KG" id="ArticleUmRiferimentoKG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoKG">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo</label></br>';
              } else
              if (um == 'HG') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'GR') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="GR" id="ArticleUmRiferimentoGR" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoGR">' + number_format(prezzo_um_riferimento / 100, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'HG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="HG" id="ArticleUmRiferimentoHG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoHG">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'KG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="KG" id="ArticleUmRiferimentoKG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoKG">' + number_format(prezzo_um_riferimento * 10, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo</label></br>';
              } else
              if (um == 'GR') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'GR') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="GR" id="ArticleUmRiferimentoGR" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoGR">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'HG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="HG" id="ArticleUmRiferimentoHG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoHG">' + number_format(prezzo_um_riferimento * 100, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'KG') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="KG" id="ArticleUmRiferimentoKG" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoKG">' + number_format(prezzo_um_riferimento * 1000, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo</label></br>';
              } else
              if (um == 'LT') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'ML') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="ML" id="ArticleUmRiferimentoML" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoML">' + number_format(prezzo_um_riferimento / 1000, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'DL') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="DL" id="ArticleUmRiferimentoDL" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoDL">' + number_format(prezzo_um_riferimento / 10, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'LT') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="LT" id="ArticleUmRiferimentoLT" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoLT">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro</label></br>';
              } else
              if (um == 'DL') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'ML') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="ML" id="ArticleUmRiferimentoML" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoML">' + number_format(prezzo_um_riferimento / 100, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'DL') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="DL" id="ArticleUmRiferimentoDL" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoDL">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'LT') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="LT" id="ArticleUmRiferimentoLT" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoLT">' + number_format(prezzo_um_riferimento * 10, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro</label></br>';
              } else
              if (um == 'ML') {
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'ML') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="ML" id="ArticleUmRiferimentoML" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoML">' + number_format(prezzo_um_riferimento, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'DL') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="DL" id="ArticleUmRiferimentoDL" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoDL">' + number_format(prezzo_um_riferimento * 100, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro</label></br>';
      
                  str += '<input class="nospace" type="radio" ';
                  (um_riferimento == 'LT') ? str += 'checked="checked" ' : str += ' ';
                  str += 'value="LT" id="ArticleUmRiferimentoLT" name="data[Article][um_riferimento]">&nbsp;&nbsp;<label class="nospace" for="ArticleUmRiferimentoLT">' + number_format(prezzo_um_riferimento * 1000, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro</label></br>';
              }          
          */
        }        
      },      
      mounted: function(){
        console.log('mounted articles');
        this.gets();
      },
      filters: {
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