<?php 
use Cake\Core\Configure; 
?>
<div class="row">
<div class="col-md-12">
    <h3 class="box-title">Conferma i dati</h3>

    <?php
    echo '<div class="row" v-if="file_contents.length>0">';
    echo '<div class="col-md-12">';

    echo '
        <div class="alert alert-info" style="font-size:18px;font-weight:bold;text-align:center;">
            Di seguito i dati dell\'excel che hai caricato
        </div>
        
        <!-- pre>{{ file_contents }}</pre -->

        <table class="table table-hover">
            <thead>
                <tr>
                    <th v-for="index in file_contents[0].length" :key="index">
                        Colonna {{ index }} <span v-if="select_import_fields[index-1]!=null">({{ select_import_fields[index-1] | translateField }})</span>
                    </th>
                    <th>Esito</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, num_row) in file_contents" :key="num_row">
                    <td v-for="(col, num_col) in row">
                        <template v-if="select_import_fields[num_col]==\'IGNORE\'">
                            <span title="Colonna esclusa" style="text-decoration: line-through;">{{ col }}</span>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'id\'">
                            <b>{{ col }}</b>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'bio\' || 
                                        select_import_fields[num_col]==\'flag_presente_articlesorders\'">
                            
                            <div v-if="col!=\'si\' && col!=\'no\'" class="alert alert-danger">
                                mi aspetto si / no, mentre è <b>{{col}}</b>
                            </div>
                            <div v-else>
                                <label class="radio-inline" :for="select_import_fields[num_col]+\'-Y-\'+num_row+\'-\'+num_col">
                                    <input type="radio" :id="select_import_fields[num_col]+\'-Y-\'+num_row+\'-\'+num_col" :name="select_import_fields[num_col]+\'-Y-\'+num_row+\'-\'+num_col" value="si" v-model="file_contents[num_row][num_col]" /> Si
                                </label>
                                <label class="radio-inline" :for="select_import_fields[num_col]+\'-N-\'+num_row+\'-\'+num_col">
                                    <input type="radio" :id="select_import_fields[num_col]+\'-N-\'+num_row+\'-\'+num_col" :name="select_import_fields[num_col]+\'-N-\'+num_row+\'-\'+num_col" value="no" v-model="file_contents[num_row][num_col]" /> No
                                </label>
                            </div>                        
                        </template>
                        <template v-if="select_import_fields[num_col]==\'codice\' || 
                                        select_import_fields[num_col]==\'codice-id\' || 
                                        select_import_fields[num_col]==\'name\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'nota\' || 
                                        select_import_fields[num_col]==\'ingredienti\'">
                            <textarea :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]"></textarea>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'qta\'">
                            <input type="number" :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'qta_um\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'prezzo\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'um\'">

                            <div v-if="!ums.includes(col)" class="alert alert-danger">
                                mi aspetto un valore tra {{ ums }}, mentre è <b>{{col}}</b>
                            </div>
                            <select v-else class="form-control" 
                                    :required="true" 
                                    :name="select_import_fields[num_col]+\'-\'+num_row+\' \'+num_col" 
                                    v-model="file_contents[num_row][num_col]">
                                <option v-for="um in ums"
                                v-bind:value="um" >
                                {{ um }}
                                </option>
                            </select>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'pezzi_confezione\'">
                            <input type="number" :name="select_import_fields[num_col]+\'-\'+num_row+\'-\'+num_col" v-model="file_contents[num_row][num_col]" />
                        </template>
                    </td>
                    <td>
                        <i class="fa-validation-ko fa fa-exclamation-circle" v-if="validazioneResults[num_row]!=null"></i>
                        <ul v-if="validazioneResults[num_row]!=null" class="li-validation-ko">
                            <li v-for="validazione in validazioneResults[num_row]">
                                Colonna {{ validazione.field_human }}: {{ validazione.error }}
                            </li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>';

    echo '</div>';
    echo '</div>'; // row 
    ?>
    <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Indietro</button>
    <button class="btn btn-success nextBtn-disabled btn-lg pull-right" type="button"
        :disabled="!ok_step3" @click="frmSubmit();">Carica il listino</button>
</div>
</div> <!-- row -->