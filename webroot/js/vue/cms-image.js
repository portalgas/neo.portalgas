var vueCmsImages = null;

$(function () {

    var router = new VueRouter({
        mode: 'history',
        routes: []
    });

    var ico_spinner = 'fa-lg fa fa-spinner fa-spin';

    vueCmsImages = new Vue({
        router,
        el: '#vue-cms-images',
        data: {
            errors: [],
            images: null,
            is_found_images: false,
            selected_images: []
        },
        methods: {
            gets: function(e) {

                let _this = this;
                $('.run-images').show();
                $('.run-images .spinner').addClass(ico_spinner);

                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                axios.defaults.headers.common['X-CSRF-Token'] = $('meta[name="csrfToken"]').attr('content');

                axios.get('/admin/api/cms-images/index')
                    .then(response => {
                        // console.log(response.data, 'cms-images');
                        $('.run-images .spinner').removeClass(ico_spinner);
                        _this.is_found_images = true;
                        _this.images = response.data.results;

                        /*
                         * immagini gia' associate alla pagina
                         */
                        _this.images.forEach(function (image) {
                            if(image.cms_pages_images.length>0)
                                _this.selected_images.push(image.id);
                        });
                    })
                    .catch(error => {
                        $('.run-run-images .spinner').removeClass(ico_spinner);
                        _this.is_found_images = false;
                        console.error("Error: " + error);
                    });
            },
            setDropzone: async function() {
                let _this = this;
                new Dropzone('div#myDropzoneImage', {
                    url: '/admin/api/cms-images/img1Upload',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')
                    },
                    beforeSend: function(request) {
                        // console.log($('meta[name="csrfToken"]').attr('content'), 'csrfToken');
                        return request.setRequestHeader('X-CSRF-Token', $('meta[name="csrfToken"]').attr('content'));
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
                            _this.gets();
                            // console.log(response, 'success response');
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
            }
        },
        mounted: function(){
            console.log('mounted vueCmsImages');
            this.gets();
            this.setDropzone();
        }
    });
});
