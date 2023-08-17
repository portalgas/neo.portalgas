<?php 
use Cake\Core\Configure; 
?>
<div class="col-md-12">
    <h3 class="box-title">Conferma i dati</h3>

    <?php
    echo '<div class="row" v-if="file_contents.length>0">';
    echo '<div class="col-md-12">';

    echo '
        <div class="alert alert-info" style="font-size:18px;font-weight:bold;text-align:center;">
            Di seguito i dati del file che hai caricato
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th v-for="index in file_contents[0].length" :key="index">
                        Colonna {{ index }}
                    </th>
                </tr>
            </htead>
            <tbody>
                <tr v-for="(row, index_row) in file_contents" :key="index_row">
                    <td v-for="(col, num_col) in row">
                        <template v-if="select_import_fields[num_col]==\'id\'">
                            <b>{{ col }}</b>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'bio\' || 
                                        select_import_fields[num_col]==\'flag_presente_articlesorders\'">
                            <input type="radio" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" value="Y"> Si
                            <input type="radio" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" value="N"> No  
                        </template>
                        <template v-if="select_import_fields[num_col]==\'codice\' || 
                                        select_import_fields[num_col]==\'name\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" :value="col" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'nota\' || 
                                        select_import_fields[num_col]==\'ingredienti\'">
                            <textarea :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col">{{col}}</textarea>
                        </template>
                        <template v-if="select_import_fields[num_col]==\'qta\'">
                            <input type="number" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" :value="col" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'prezzo\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" :value="col" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'um\'">
                            <input type="text" :name="select_import_fields[num_col]+\'-\'+index_row+\' \'+num_col" :value="col" />
                        </template>
                        <template v-if="select_import_fields[num_col]==\'pezzi_confezione\'">
                            <input type="number" :name="select_import_fields[num_col]+\'-\'+index_row+\'-\'+num_col" :value="col" />
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>';

    echo '</div>';
    echo '</div>'; // row 
    ?>
    <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Indietro</button>
    <button class="btn btn-success btn-lg pull-right" type="submit"
        :disabled="ok_step3">Carica il listino</button>
</div>