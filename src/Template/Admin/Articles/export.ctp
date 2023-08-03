<?php 
use Cake\Core\Configure; 
$user = $this->Identity->get();

echo $this->Html->script('jquery/ui/jquery-ui.min', ['block' => 'scriptPageInclude']); 
echo $this->Html->css('jquery/ui/jquery-ui.min', ['block' => 'css']); 
?>

<style>
.kt-portlet {
    -webkit-box-shadow: 0px 0px 13px 0px rgba(52, 59, 62, 0.15);
    box-shadow: 0px 0px 13px 0px rgba(52, 59, 62, 0.15);
}
.kt-portlet {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);
    box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);
    background-color: #ffffff;
    margin-bottom: 20px;
    border-radius: 4px;
}  
.kt-portlet .kt-portlet__head {
    -webkit-transition: height 0.3s;
    transition: height 0.3s;
}
.kt-portlet .kt-portlet__head {
    -webkit-transition: left 0.3s, right 0.3s, height 0.3s;
    transition: left 0.3s, right 0.3s, height 0.3s;
}
.kt-portlet .kt-portlet__head {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: stretch;
    -ms-flex-align: stretch;
    align-items: stretch;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    position: relative;
    padding: 0 25px;
    border-bottom: 1px solid #ebedf2;
    min-height: 60px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
.kt-portlet .kt-portlet__head .kt-portlet__head-label {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-line-pack: flex-first;
    align-content: flex-first;
}
.kt-portlet .kt-portlet__head .kt-portlet__head-label .kt-portlet__head-title {
    margin: 0;
    padding: 0;
    font-size: 1.2rem;
    font-weight: 500;
    color: #48465b;
}
.kt-portlet .kt-portlet__head .kt-portlet__head-toolbar {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-line-pack: end;
    align-content: flex-end;
}
.kt-badge.kt-badge--brand {
    color: #ffffff;
    background: #009CDE;
}
.kt-badge.kt-badge--lg {
    height: 35px;
    width: 35px;
    font-size: 1rem;
}

.kt-badge {
    padding: 0;
    margin: 0;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    height: 18px;
    width: 18px;
    border-radius: 50%;
    font-size: 0.8rem;
}
.kt-portlet .kt-portlet__body {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    padding: 25px;
    border-radius: 4px;
}
#source-fields, #export-fields, #default-fields {
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
}
#source-fields li, 
#export-fields li, 
#default-fields li {
    width: 100%;
    height: auto;
    list-style-type: none;
    background-color: #ffffff;
    box-shadow: 0px 0px 13px 0px rgba(52, 59, 62, 0.15);
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ffffff;
    border-radius: 4px;
    box-sizing: border-box;
    cursor: move;
}
#default-fields li {
    cursor: default;
}
#source-fields li span, 
#export-fields li span,
#default-fields li span  {
    font-size: 14px;
    font-weight: 500;
    color: #48465b;
}
#source-fields li code, 
#export-fields li code,
#default-fields li code {
    font-size: 10px;
    color: #48465b;
    padding: 4px 10px 0px 4px;
    position: relative;
}
</style>
 
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Export');?></h3>

      <div class="box-tools pull-right">
      </div>
    <!-- /.box-header -->
    <div class="box-body">

<?php 
echo $this->Form->create(null, ['id' => 'frmExport', 'type' => 'POST']); 
echo $this->Form->control('export_fields', ['type' => 'hidden', 'id' => 'export_fields']);
?>
<fieldset>
    <legend>Export</legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-12 col-12">';
    ?>

<div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <span class="kt-badge kt-badge--brand kt-badge--lg mr-3">1</span> Scegli il produttore
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body">
                <?php 
                    echo $this->HtmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations);
                ?>                
            </div>
        </div>

    <?php 
    echo '</div>';
    echo '</div>';  // row 

    /*
     * Scegli le colonne
     */
    echo '<div class="row-no-margin">';
    echo '<div class="col-12 col-sm-6 col-lg-6">';
    ?>

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        <span class="kt-badge kt-badge--brand kt-badge--lg mr-3">2</span> Scegli quali campi esportare
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <label id="fields-select-all" style="cursor: pointer" class="label label-primary" >Sceglili tutti</label>
                </div>
            </div>
            <div class="kt-portlet__body">
                <ul id="source-fields" class="connectedSortable control-height">
                    <?php 
                    foreach($source_fields as $key => $source_field) {
                        echo '<li id="'.$key.'" class="ui-state-default d-flex justify-content-between ui-sortable-handle">';
                        echo '<span><i class="fa fa-arrows-alt"></i> '.$source_field['label'].'</span>';
                        echo '<code> es. '.$source_field['nota'].' <span class="bg" style="background-color: 0"></span></code>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>

    <?php 
    /*
     * Ordina le colonne
     */   
    echo '</div>'; 
    echo '<div class="col-12 col-sm-6 col-lg-6">';
    ?>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title"><span class="kt-badge kt-badge--brand kt-badge--lg mr-3">3</span> Campi esportabili (ordinali come preferisci)</h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body">

                <ul id="default-fields" class="control-height">
                    <?php 
                    foreach($default_fields as $key => $default_field) {
                        echo '<li id="'.$key.'" class="ui-state-default d-flex justify-content-between ui-sortable-handle">';
                        echo '<span><i class="fa fa-thumb-tack"></i> '.$default_field['label'].'</span>';
                        echo '<code> es. '.$default_field['nota'].' <span class="bg" style="background-color: 0"></span></code>';
                        echo '</li>';
                    }
                    ?>
                </ul>

                <ul id="export-fields" class="connectedSortable control-height">
                    <?php 
                    foreach($export_fields as $key => $export_field) {
                        echo '<li id="'.$key.'" class="ui-state-default d-flex justify-content-between ui-sortable-handle">';
                        echo '<span><i class="fa fa-arrows-alt"></i> '.$export_field['label'].'</span>';
                        echo '<code> es. '.$export_field['nota'].' <span class="bg" style="background-color: 0"></span></code>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    <?php 
    echo '</div>';  
    echo '</div>';


    echo '<div class="row">';
    echo '<div class="col col-md-12 col-12">';
    echo '<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-file-excel-o"></i> '.__('Export').'</button>';
    echo '</div>'; 
    echo '</div>'; 
    ?>
</fieldset>
</div> <!-- box-body -->
</div>
<?php
echo $this->Form->end() 
?>

<?php 
$js = "
$( function() {
    $('#source-fields, #export-fields').sortable({
        connectWith: '.connectedSortable'
    });

    $('#fields-select-all').on('click', function (e) {
        e.preventDefault();
        $('#source-fields li').each(function() {
            var _this = $(this);
            _this.appendTo('#export-fields');
           // $('#export-fields').sortable('option', 'receive')(null, { item: _this });
        });        
    });

    $('form#frmExport').on('submit', function (e) {
        // e.preventDefault();

        let supplier_organization_id = $('#supplier_organization_id').val();
        console.log('supplier_organization_id '+supplier_organization_id , 'submit');
        if(supplier_organization_id=='') {
            alert('Seleziona il produttore');
            return false;
        }

        let tot_fields = $('ul#export-fields li').length;
        console.log('tot_fields '+tot_fields , 'submit');
        if(tot_fields==0) {
            alert('Seleziona almeno un campo da esportare');
            return false;
        }
        
        /*
         * popolo il campo hidden con i campi da esportare
         */
        let export_fields = '';
        $('#export_fields').val('');
        $.each($('ul#export-fields li'), function( key, field ) {
            // console.log(key + ': ' + $(field).attr('id'), 'submit');
            export_fields += $(field).attr('id')+';';
        });
        export_fields = export_fields.substring(0, (export_fields.length-1));
        console.log(export_fields, 'submit');
        $('#export_fields').val(export_fields);

        return true;
    });  
});
";
$this->Html->scriptBlock($js, ['block' => true]);