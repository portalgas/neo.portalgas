<?php
use Cake\Core\Configure;
echo $this->Html->script('vue/exports', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Export Docs to order'), 'subtitle' => __('Management')], ['home']);

echo $this->HtmlCustomSite->boxOrder($order);

echo '<div id="vue-exports">';
echo $this->Form->create(null, ['role' => 'form', 'id' => 'frm']);
?>
  <input type="hidden" name="organization_id" value="<?php echo $order->organization_id;?>" />
  <input type="hidden" name="order_type_id" value="<?php echo $order->order_type_id;?>" />
  <input type="hidden" name="order_id" value="<?php echo $order->id;?>" />
  <input type="hidden" name="_csrfToken" autocomplete="off" value=<?php echo json_encode($this->request->getParam('_csrfToken')) ?> />
  
<section class="">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
            <h3 class="box-title"><?php echo __('List'); ?></h3>
        </div>
        <div class="box-body">
<?php        
echo '<div class="row">'; 
echo '<div class="col-md-4">'; 
echo $this->Form->control('print_id', ['type' => 'radio', 'label' => 'Tipologie di stampe', 
            'options' => $exports, 
            'default' => '',
            'v-model' => 'print_id',
            '@click' => 'htmlGets'
 ]);
echo '</div>';

/* 
 * opzioni di stampa 
 * devono avere la classe options, in exports.js li passo alla chiamata ajax
 */
echo '<div class="col-md-5">'; 
$this->Form->setTemplates([
  'nestingLabel' => '{{hidden}}<label{{attrs}} class="radio-inline">{{input}}{{text}}</label>',
  'radioWrapper' => '{{label}}',
]);
echo '<div>';
echo '<label class="radio-label">Visualizza la consegna dell\'ordine titolare</label><br />'; 
echo $this->Form->radio('delivery_order_parent', ['Y' => 'Si', 'N' => 'No'], 
                                                  ['class' => 'options', 'default' => 'N', 
                                                  '@click' => 'htmlGets']);
echo '</div>';
echo '<div>';
echo '<label class="radio-label">Visualizza le consegne degli ordini dei gruppi</label><br />'; 
echo $this->Form->radio('deliveries_orders', ['Y' => 'Si', 'N' => 'No'], 
                                             ['class' => 'options', 'default' => 'Y', 
                                              '@click' => 'htmlGets']);
echo '</div>';
echo '</div>'; // row

/* 
 * formato di stampa 
 */
echo '<div class="col-md-2">'; 
$this->Form->setTemplates([
  'nestingLabel' => '{{hidden}}<label{{attrs}} class="radio">{{input}}{{text}}</label>',
  'radioWrapper' => '{{label}}',
]);
echo '<div>';
echo '<label class="radio-label">Formato di stampa</label>'; 
echo $this->Form->radio('format', ['PDF' => 'Pdf', 'XLSX' => 'Excel'], 
                                  ['default' => 'PDF']);
echo '</div>';
echo '</div>'; // row

echo '</div>';


echo '</div> <!-- /.box-body --> ';
echo '</div> <!-- /.box --> ';
echo '</div>';
echo '</div>';
echo '</section>';

echo $this->Form->button('<i class="fa fa-file"></i> '.__('Export'), ['type' => 'button', 'class' => 'btn btn-success pull-right btn-block', 'style' => 'margin-bottom:25px', '@click' => 'exportGets']);
?>

<div v-if="is_run" class="box-body table-responsive no-padding text-center" style="margin: 150px">
  <div><i class="fa-lg fa fa-spinner fa-spin"></i></div>
</div>
<div v-if="!is_run && print_id!=null">
  <section class="box-export">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-body" v-html="$options.filters.html(print_results)">
          </div> <!-- /.box-body -->
        </div> <!-- /.box -->
      </div>
    </div>
  </section>
</div>
<?php 
echo $this->Form->end();
?>
</div> <!-- #vue-exports -->

<style>
.radio-inline {
  min-width: 100px;
  padding: 5px 0
}  
</style>