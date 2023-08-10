<?php 
use Cake\Core\Configure; 
$user = $this->Identity->get();

echo $this->Html->script('vue/articlesImport', ['block' => 'scriptPageInclude']);

echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']); 
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']); 

echo $this->Html->script('jquery/ui/jquery-ui.min', ['block' => 'scriptPageInclude']); 
echo $this->Html->css('jquery/ui/jquery-ui.min', ['block' => 'css']); 

$js = "var import_fields = ".json_encode($import_fields);
$this->Html->scriptBlock($js, ['block' => true]);
?>  
<div id="vue-articles-import">
<?php 
echo $this->Form->create(null, ['id' => 'frmExport', 'type' => 'POST']); 
?>
<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Import');?></h3>

      <div class="box-tools pull-right">
      </div>
    <!-- /.box-header -->
    <div class="box-body" style="overflow-x: auto;">
    <?php
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
    echo '<div class="row" v-if="supplier_organization.supplier.img1!=\'\'">';
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

    /* 
     * listino articoli gestito dal GAS
     */
    echo '<template v-if="supplier_organization.owner_articles==\'REFERENT\'">';
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="dropzone" id="my-dropzone"></div>';
    echo '</div>';
    echo '</div>'; // row  
    
    echo '<div class="row" v-if="file_contents.length>0">';
    echo '<div class="col-md-12">';
    /*
    $i=0;
    foreach($export_fields as $key => $export_field) {
        if($i==0) 
            echo '<div id="draggable" class="btn-group btn-group-justified">';
        if($i==4) 
            echo '</div><div id="draggable" class="btn-group btn-group-justified">';

        echo '<div id="'.$key.'" data-attr-label="'.$export_field['label'].'" class="draggable btn btn-primary">';
        echo '<span><i class="fa fa-arrows-alt"></i> '.$export_field['label'].'</span>';
        echo '<code> es. '.$export_field['nota'].' <span class="bg" style="background-color: 0"></span></code>';
        echo '</div>';
        $i++;
    }
    echo '</div>';
    */

    echo '
        <table class="table table-hover">
            <thead>
                <tr id="droppable">
                    <th v-for="index in file_contents[0].length" :key="index">
                        Colonna {{ index }}
                        <select :name = "\'option-field-\'+index" 
                                :id= "\'option-field-\'+index" 
                                @change = "setOptionsFields(index)"
                                class="form-control">
                                <option v-for="(import_field, id) in import_fields" 
                                        :value="id" 
                                        v-html="$options.filters.html(import_field)">
                                </option>
                        </select>
                    </th>
                </tr>
            </htead>
            <tbody>
                <tr v-for="(file_contents, index_row) in file_contents" :key="index_row">
                    <td v-for="file_content in file_contents">
                        {{ file_content }}
                    </td>
                </tr>
            </tbody>
        </table>';

    echo '</div>';
    echo '</div>'; // row 

    echo '<br />';

    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo $this->Form->button(__('Import'), ['id' => 'submit', 'class' => 'btn btn-primary btn-block']); 
    echo '</div>';
    echo '</div>'; // row  
    echo '</template>';
    
    echo '</template>';
?>
</div> <!-- box-body -->
<?php
echo $this->Form->end() 
?>
</div> <!-- vue-articles-import -->
<?php 
$js = "
$( function() {
    $('form#frmImport').on('submit', function (e) {
        // e.preventDefault();

        let supplier_organization_id = $('#supplier_organization_id').val();
        console.log('supplier_organization_id '+supplier_organization_id , 'submit');
        if(supplier_organization_id=='') {
            alert('Seleziona il produttore');
            return false;
        }        

        return true;
    });        
});

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



