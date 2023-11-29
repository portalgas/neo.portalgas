<?php 
use Cake\Core\Configure; 
$user = $this->Identity->get();
?>  
<style>
h3.box-title {
    margin-bottom: 15px !important;
    font-size: 20px !important;
}
.stepwizard-step p {
    margin-top: 10px;
}
.stepwizard-row {
    display: table-row;
}
.stepwizard {
    display: table;
    width: 50%;
    position: relative;
}
.stepwizard-step button[disabled] {
    opacity: 1 !important;
    filter: alpha(opacity=100) !important;
}
.stepwizard-row:before {
    top: 14px;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 100%;
    height: 1px;
    background-color: #ccc;
    z-order: 0;
}
.stepwizard-step {
    display: table-cell;
    text-align: center;
    position: relative;
}
.btn-circle {
    width: 30px;
    height: 30px;
    text-align: center;
    padding: 6px 0;
    font-size: 12px;
    line-height: 1.428571429;
    border-radius: 15px;
}
.box-title {
    margin-bottom: 25px;
}
.option-ignore {
    background-color: #fbf049;
} 
.my-alert {
    font-size:18px;
    font-weight:bold;
    text-align:center;
}
.fa-validation-ko {
  color: red;
  font-size: 24px;
  float: left;
  margin-right: 10px;
}
.li-validation-ko {
    padding:0px;
    list-style-type: none;
}
</style>    
<?php
echo $this->Html->script('vue/articlesImport.js?v=20231129', ['block' => 'scriptPageInclude']);

echo $this->Html->script('jquery/ui/jquery-ui.min', ['block' => 'scriptPageInclude']); 
echo $this->Html->css('jquery/ui/jquery-ui.min', ['block' => 'css']); 
?> 
<div id="vue-articles-import">

<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Importa listino articoli del produttore</h3>
      <div class="box-tools pull-right"></div>
    </div> <!-- /.box-header -->
    <div class="box-body table-responsive" style="overflow-x: auto;">

    <!-- loader globale -->
    <div class="loader-global" v-if="is_run">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
        
    <template v-if="!is_run && !importResult">
        
        <div class="stepwizard col-md-offset-3">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Scegli il produttore</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                    <p>Carica il file excel</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                    <p>Conferma i dati e importa</p>
                </div>
            </div>
        </div>

        <?php 
        echo $this->Form->create(null, ['id' => 'frmImport', 
                                        'type' => 'POST', 
                                        'v-on:submit.prevent' => 'frmSubmit(e)',
                                        'ref' => 'form']); 
        echo $this->Form->hidden('select_import_fields');
        echo $this->Form->hidden('is_first_row_header');
        echo $this->Form->hidden('full_path');
        ?>
            <div class="row-disabled setup-content" id="step-1">
                <?php require('import-step1.ctp');?>
            </div>
            <div class="row-disabled setup-content" id="step-2">
                <?php require('import-step2.ctp');?>
            </div>
            <div class="row-disabled setup-content" id="step-3">
                <?php require('import-step3.ctp');?>
            </div>
        <?php    
        echo $this->Form->end();
        ?>

    </template>
    <template v-if="importResult">

    <div class="row">
        <div class="col-md-12 text-center">
            <div class="alert alert-success">
                Importazione avvenuta con successo
            </div>     
            <a :href="'/admin/articles/index-quick?search_supplier_organization_id='+supplier_organization.id"><button class="btn btn-primary">clicca qui per visualizzare il listino articoli del produttore {{ supplier_organization.name }}</button></a>
        </div>
    </div>
    <div class="row" style="margin:15px 0;" 
                v-if="supplier_organization.supplier.img1!=''">
        <div class="col-md-12 text-center">
            <img style="max-width:250px" class="img-responsive" v-bind:src="supplier_organization.img1" /></div>
        </div>
    </div>

    
    </template>
<?php
echo '</div> <!-- box-body -->';
echo '</div> <!-- vue-articles-import -->';

/* 
 * gestione STEP
 */
$js = "
$(document).ready(function () {
    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn'),
        allPrevBtn = $('.prevBtn');
  
    allWells.hide();
  
    navListItems.click(function (e) {
        e.preventDefault();
        var target = $($(this).attr('href')),
            item = $(this);
  
        if (!item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            item.addClass('btn-primary');
            allWells.hide();
            target.show();
        }
    });
    
    allPrevBtn.click(function(){
        var curStep = $(this).closest('.setup-content'),
            curStepBtn = curStep.attr('id'),
            prevStepWizard = $('div.setup-panel div a[href=\"#' + curStepBtn + '\"]').parent().prev().children('a');
  
            prevStepWizard.removeAttr('disabled').trigger('click');

            $('html, body').animate({ scrollTop: 0 }, 'fast');
    });
  
    allNextBtn.click(function(){
        var curStep = $(this).closest('.setup-content'),
            curStepBtn = curStep.attr('id'),
            nextStepWizard = $('div.setup-panel div a[href=\"#' + curStepBtn + '\"]').parent().next().children('a');
            
            nextStepWizard.removeAttr('disabled').trigger('click');

            $('html, body').animate({ scrollTop: 0 }, 'fast');
    });
  
    $('div.setup-panel div a.btn-primary').trigger('click');
});";
$this->Html->scriptBlock($js, ['block' => true]);