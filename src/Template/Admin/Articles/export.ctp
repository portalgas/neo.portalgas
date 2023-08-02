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
#fields-sortable-source, #fields-sortable-destination, #fields-default {
    width: 100%;
    height: auto;
    margin: 0;
    padding: 0;
}
#fields-sortable-source li, 
#fields-sortable-destination li, 
#fields-default li {
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
#fields-default li {
    cursor: default;
}
#fields-sortable-source li span, 
#fields-sortable-destination li span,
#fields-default li span  {
    font-size: 14px;
    font-weight: 500;
    color: #48465b;
}
#fields-sortable-source li code, 
#fields-sortable-destination li code,
#fields-default li code {
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

<?= $this->Form->create(null, ['id' => 'frmExport', 'type' => 'GET']); ?>
<fieldset>
    <legend>Export</legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-12 col-12">';
    $options = [];
    $options['ctrlDesACL'] = false;
    $options['id'] = 'search_supplier_organization_id'; // non c'e' il bind in supplierOrganization.js
    $options['default'] = $search_supplier_organization_id;
    (count($suppliersOrganizations)==1) ? $options['empty'] = false: $options['empty'] = true;
    $options['v-model'] = 'search_supplier_organization_id';
    $options['@change'] = 'changeSearchSupplierOrganizationId';
    ?>

<div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title"><span class="kt-badge kt-badge--brand kt-badge--lg mr-3">1</span> Scegli il produttore</h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body">
                <?php 
                    echo $this->HtmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations, $options);
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
                    <h3 class="kt-portlet__head-title"><span class="kt-badge kt-badge--brand kt-badge--lg mr-3">2</span> Scegli le colonne</h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body">
                <ul id="fields-sortable-source" class="connectedSortable control-height">
                    <li class="ui-state-default d-flex justify-content-between ui-sortable-handle" id="pratica_note">
                        <span><i class="fa fa-arrows-alt"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
                    <li class="ui-state-default d-flex justify-content-between ui-sortable-handle" id="pratica_note">
                        <span><i class="fa fa-arrows-alt"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
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
                    <h3 class="kt-portlet__head-title"><span class="kt-badge kt-badge--brand kt-badge--lg mr-3">3</span> Ordina le colonne</h3>
                </div>
                <div class="kt-portlet__head-toolbar"></div>
            </div>
            <div class="kt-portlet__body">

                <ul id="fields-default" class="control-height">
                    <li class="d-flex justify-content-between" id="pratica_note">
                        <span><i class="fa fa-ban"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
                    <li class="d-flex justify-content-between" id="pratica_note">
                        <span><i class="fa fa-ban"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
                </ul>

                <ul id="fields-sortable-destination" class="connectedSortable control-height">
                    <li class="ui-state-default d-flex justify-content-between ui-sortable-handle" id="pratica_note">
                        <span><i class="fa fa-arrows"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
                    <li class="ui-state-default d-flex justify-content-between ui-sortable-handle" id="pratica_note">
                        <span><i class="fa fa-arrows"></i> PRATICA: NOTE</span>
                        <code> es. Note pratica <span class="bg" style="background-color: 0"></span></code>
                    </li>
                </ul>
            </div>
        </div>
    <?php 
    echo '</div>';  
    echo '</div>';


    echo '<div class="row">';
    echo '<div class="col col-md-12 col-12">';
    echo '<button type="button" class="btn btn-primary btn-block" @click="gets()">'.__('Export').'</button>';
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
  $('#fields-sortable-source, #fields-sortable-destination').sortable({
    connectWith: '.connectedSortable'
  });
});
";
$this->Html->scriptBlock($js, ['block' => true]);