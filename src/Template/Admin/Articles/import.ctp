<?php 
use Cake\Core\Configure; 
$user = $this->Identity->get();
?>  
<style>
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
</style>    
<?php
echo $this->Html->script('vue/articlesImport', ['block' => 'scriptPageInclude']);

echo $this->Html->script('jquery/ui/jquery-ui.min', ['block' => 'scriptPageInclude']); 
echo $this->Html->css('jquery/ui/jquery-ui.min', ['block' => 'css']); 
?> 
<div id="vue-articles-import">

<div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Importa listino articoli del produttore</h3>

      <div class="box-tools pull-right">
      </div>
    <!-- /.box-header -->
    <div class="box-body" style="overflow-x: auto;">

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
                <p>Conferma i dati</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                <p>Esito import</p>
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
    <div class="row-disabled setup-content" id="step-4">
        <?php require('import-step4.ctp');?>
    </div>
<?php    
echo $this->Form->end();
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