<?php 
use Cake\Core\Configure;

$id = ucfirst($article['id']);
// debug($article['img1']);
echo '<div class="dropzone" id="my-dropzone'.$id.'"></div>';

if(!empty($article['img1_size'])) {
	$js_img_edit = "
		let myDropzone = this;
		let mockFile = { name: 'Foto articolo', size: ".$article['img1_size']." };
		myDropzone.displayExistingFile(mockFile, '".$article['img1']."');
		this.files.push(mockFile)
	";	
}

$js = "
  Dropzone.options.myDropzone".$id." = {
    url: '/admin/articles/img1/upload/".$article['organization_id']."/".$id."', 
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

		console.log('myDropzone".$id."', 'init');

        ".$js_img_edit." 

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
			$.post('/admin/articles/img1/delete/".$article['organization_id']."/".$article['id']."',); 
		});		
	},
    accept: function(file, done) {
      if (file.name == 'justinbieber.jpg') {
        done('dropzone eseguito');
      }
      else { done(); }
    }
  };  
";

$this->Html->scriptBlock($js, ['block' => true]);
