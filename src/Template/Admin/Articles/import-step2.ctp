<?php 
use Cake\Core\Configure; 

$js = "var import_fields = ".json_encode($import_fields);
$this->Html->scriptBlock($js, ['block' => true]);

echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']); 
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']); 
?>
<div class="row">
<div class="col-md-12">
    <h3 class="box-title">Carica il file excel</h3>
    <?php
    /* 
     * listino articoli gestito dal GAS
     */
    echo '<template v-if="supplier_organization.owner_articles==\'REFERENT\'">';

    echo '<div class="row" style="margin-bottom:15px;">';
    echo '<div class="col-md-12">';
    echo '<a class="btn-block btn" 
            :class="is_first_row_header ? \'btn-danger\' : \'btn-success\'" 
            @click="toggleIsFirstRowHeader()">
        <span v-if="is_first_row_header">La prima riga è l\'intestazione, non verrà considerata</span>
        <span v-if="!is_first_row_header">La prima riga NON è l\'intestazione, verrà importata</span>
        </a>'; 
    echo '</div>';
    echo '</div>'; // row 

    echo '<div class="row" style="margin-bottom:15px;">';
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
        <div class="alert alert-info" style="font-size:18px;font-weight:bold;text-align:center;">
            Di seguito le prime <b>5</b> righe estratte dal file che hai caricato<br />
            per ogni colonna indica che campo dell\'articolo corrisponde
        </div>
        
        <table class="table table-hover">
            <thead>
                <tr id="droppable">
                    <th v-for="index in file_contents[0].length" :key="index">
                        Colonna {{ index }}
                        <select :name = "\'option-field-\'+(index-1)" 
                                :id= "\'option-field-\'+(index-1)" 
                                @change = "setOptionsFields(index-1)"
                                class="form-control">
                                <option v-for="(import_field, id) in import_fields" 
                                        :value="id" 
                                        :class = "id==\'IGNORE\' ? \'option-ignore\' : \'\'"
                                        v-html="$options.filters.html(import_field)">
                                </option>
                        </select>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, index_row) in file_contents" v-if="index_row<5" :key="index_row">
                    <td v-for="col in row">
                        {{ col }}
                    </td>
                </tr>
            </tbody>
        </table>';

    echo '</div>';
    echo '</div>'; // row 

    echo '<br />';

    echo '<div class="row" v-if="!ok_step2 && num_excel_fields>0">';
    echo '<div class="col-md-12">';
    echo '<div class="alert alert-info" style="font-size:18px;font-weight:bold;text-align:center;">
    Hai configurato {{ fields_to_config }} su {{ num_excel_fields }}</div>';
    echo '</div>';
    echo '</div>'; // row 
        
    echo '</template>';
    ?>
    <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Indietro</button>
    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"
            :disabled="!ok_step2">Avanti</button>
</div>
</div> <!-- row -->

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