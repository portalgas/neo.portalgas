<?php
use Cake\Core\Configure;
echo $this->Html->script('vue/exports', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Export Docs to order'), 'subtitle' => __('Management')], ['home']);

echo $this->HtmlCustomSite->boxOrder($order);

echo '<div id="vue-exports">';
echo $this->Form->create(null, ['role' => 'form']);
?>
  <input type="hidden" name="organization_id" value="<?php echo $order->organization_id;?>" />
  <input type="hidden" name="order_type_id" value="<?php echo $order->order_type_id;?>" />
  <input type="hidden" name="order_id" value="<?php echo $order->id;?>" />

  
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
echo '<div class="col-md-12">'; 
echo $this->Form->control('print_id', ['type' => 'radio', 'label' => 'Tipologie di stampe', 
            'options' => $exports, 
            'default' => '',
            'v-model' => 'print_id',
            '@click' => 'htmlGets'
 ]);
echo '</div>';
echo '</div>';
?>
        </div> <!-- /.box-body -->
      </div> <!-- /.box -->
    </div>
  </div>
</section>

<?php 
echo $this->Form->button('<i class="fa fa-file-pdf-o"></i> '.__('Export to PDF'), ['type' => 'button', 'class' => 'btn btn-success pull-right btn-block', 'style' => 'margin-bottom:25px', '@click' => 'pdfGets']);
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