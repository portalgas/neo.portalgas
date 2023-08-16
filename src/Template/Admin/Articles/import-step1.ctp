<?php 
use Cake\Core\Configure; 
?>
<div class="col-md-12">
    <h3>Scegli il produttore</h3>
    <?php
    /* 
     * filtri di ricerca
     */
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    $options = ['options' => $suppliersOrganizations,
                'escape' => false,
                'class' => 'select2- form-control',
                'label' => __('SupplierOrganization'),
                'empty' => Configure::read('HtmlOptionEmpty'),
                'v-model' => 'supplier_organization_id',
                '@change' => 'getSuppliersOrganization(event)'];
    echo $this->Form->control('supplier_organization_id', $options);
    echo '</div>';
    echo '</div>'; // row  

    echo '<template v-if="supplier_organization.name!=null">';    
        /* 
        * anagrafica produttore
        */
        echo '<div class="row" style="margin-bottom:15px;" 
                    v-if="supplier_organization.supplier.img1!=\'\'">';
        echo '  <div class="col-md-12">
            <img style="max-width:250px" class="img-responsive" v-bind:src="supplier_organization.img1" /></div>';
        echo '</div>'; 

        /* 
        * listino articoli NON gestito dal GAS
        */
        echo '<template v-if="supplier_organization.owner_articles!=\'REFERENT\'">';
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<div class="alert alert-danger">Non puoi importare il listino del produttore {{supplier_organization.name}} perchè è gestito {{supplier_organization.owner_articles | ownerArticlesLabel}}'; 
        echo '</div>';
        echo '</div>'; // row  
        echo '</template>';    
    echo '</template>';    
    ?>  

    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Avanti</button>
</div>

<?php 
$js = "
Dropzone.options.myDropzone = { // camelized version of the `id`
    url: '/admin/api/articles-import/upload', 
    headers: {
        'X-CSRF-TOKEN': csrfToken
    },                    
    beforeSend: function(request) {
        return request.setRequestHeader('X-CSRF-Token', csrfToken);
    },    
    dictDefaultMessage: 'Trascina qui il file excel da importare',
    dictFileTooBig: 'Il file è troppo grande ({{filesize}}MiB). Grande massima consentita: {{maxFilesize}}MiB.',
    dictInvalidFileType: 'Non puoi uploadare file di questo tipo.',
    dictResponseError: 'Server responded with {{statusCode}} code',
    dictCancelUpload: 'Cancel upload',
    dictRemoveFile: 'Elimina file',
    dictCancelUploadConfirmation: 'Are you sure you want to cancel this upload?',
    dictMaxFilesExceeded: 'Non puoi uploadare più file.',	
    parallelUploads: 1,
    addRemoveLinks: true,
    uploadMultiple:false,
    maxFiles: 1,
    acceptedFiles: '.xlsx',
    paramName: 'file', // The name that will be used to transfer the file
    maxFilesize: 5, // MB
    init: function() {

        console.log('myDropzone', 'init');

        this.on('addedfile', function(file) {

            _this.file_errors = [];
            _this.file_contents = [];

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
        this.on('removedfile', function(file) {
            console.log(file, 'removedfile'); 
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
?>