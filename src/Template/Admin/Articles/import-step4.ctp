<?php 
use Cake\Core\Configure; 
?>
<div class="col-md-12">
    <h3 class="box-title">Esito import</h3>

    <div class="row">
        <div class="col-md-12">

            <div v-if="is_run" class="box-spinner"> 
                <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>  
            </div>
            <div v-if="!is_run">
                <pre>{{ results }}</pre>
            </div>
            
        </div>
    </div> <!-- row -->
    
</div>