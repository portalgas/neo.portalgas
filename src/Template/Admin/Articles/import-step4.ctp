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

            <pre>{{ results }}</pre>
            
            <div v-if="!is_run">
                <div class="alert alert-info my-alert">
                    Di seguito gli articoli non importati perchè presentavano errori 
                </div>
                
                <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Riga dell'excel</th>
                        <th>Errore</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="result in results" :key="result.numRow">
                        <td>{{ result.numRow +1 }}</td>
                        <td>
                            <ul>
                                <li v-for="(msg, field) in result.msg">
                                    {{ field }}: {{ msg }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
                </table>
                                     
            </div>
         
            <button class="btn btn-success nextBtn btn-lg pull-right" type="button"
                    @click="frmSubmit();">Carica il listino</button>

        


        </div>
    </div> <!-- row -->
    
</div>