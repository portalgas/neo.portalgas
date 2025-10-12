var vueCmsDocs = null;

$(function () {

    var router = new VueRouter({
        mode: 'history',
        routes: []
    });

    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    vueCmsDocs = new Vue({
        router,
        el: '#vue-cms-docs',
        data: {
            errors: [],
            docs: null,
            is_found_docs: false,
            selected_docs: []
        },
        methods: {
            gets: function(e) {

                let _this = this;
                $('.run-docs').show();
                $('.run-docs .spinner').addClass(ico_spinner);

                /*
                 * estraggo eventuale cms_page_id dalla pagina
                 */
                let cms_page_id = document.getElementById('cms_page_id');
                if(typeof cms_page_id==='undefined' || cms_page_id==null)
                    cms_page_id=0;
                else
                    cms_page_id = cms_page_id.value;

                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                axios.defaults.headers.common['X-CSRF-Token'] = $('meta[name="csrfToken"]').attr('content');

                axios.get('/admin/api/cms-docs/index/'+cms_page_id)
                    .then(response => {
                        /* console.log(response.data); */
                        $('.run-docs .spinner').removeClass(ico_spinner);
                        _this.is_found_docs = true;
                        _this.docs = response.data.results;

                        /*
                         * docs gia' associate alla pagina
                         */
                        _this.docs.forEach(function (doc) {
                            // console.log(doc.cms_page, 'doc');
                            if(doc.cms_page!=null) {
                                _this.selected_docs.push(doc.id);
                            }
                        });
                        // console.table(_this.selected_images, 'selected_images');
                    })
                    .catch(error => {
                        $('.run-docs .spinner').removeClass(ico_spinner);
                        _this.is_found_docs = false;
                        console.error("Error: " + error);
                    });
            },
            setDropzone: async function() {
                let _this = this;
                new Dropzone('div#myDropzoneDoc', {
                        url: '/admin/api/cms-docs/doc1Upload',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')
                        },
                        beforeSend: function(request) {
                            // console.log($('meta[name="csrfToken"]').attr('content'), 'csrfToken');
                            return request.setRequestHeader('X-CSRF-Token', $('meta[name="csrfToken"]').attr('content'));
                        },
                        dictDefaultMessage: 'Trascina qui il dcumento',
                        dictRemoveFile: 'Elimina documento',
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
                        acceptedFiles: '.pdf',
                        paramName: 'doc1', // The name that will be used to transfer the file
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
                                _this.gets();
                                // console.log(response, 'success response');
                            });
                            this.on('removedfile', function(file) {
                                /* console.log(file, 'removedfile'); */
                                $.ajax({
                                    url: '/admin/api/cms-docs/doc1Delete/',
                                    type: 'post',
                                    data: {name: file.name},
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
            }
        },
        mounted: function(){
            console.log('mounted vueCmsDocs');
            this.gets();
            this.setDropzone();
        }
    });
});
