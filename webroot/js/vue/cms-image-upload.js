Dropzone.options.myDropzoneImage = {
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
        this.on('error', function(file, errorMessage) {
            console.error("Error uploading file:", errorMessage);
            // Display the error message to the user
            if(typeof errorMessage.msg !== 'undefined')
                alert("Errore uplodando il file: " + errorMessage.msg);
            else
                alert("Errore uplodando il file");

            this.removeAllFiles();
        });
        this.on('success', function(file, response) {
            // console.log(file, 'images-upload success file');
            // console.log(response, 'images-upload success response');
            if(response.esito) {

            }
            vueCmsImages.gets();
            this.removeFile(this.files[0]);
            // window.location.reload();
            // console.log(response, 'success response');
        });
        this.on('removedfile', function(file) {
            // console.log(file, 'removedfile');
            $.ajax({
                url: '/admin/api/cms-images/delete/',
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
};
