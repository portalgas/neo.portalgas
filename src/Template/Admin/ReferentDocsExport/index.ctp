<?php
use Cake\Core\Configure;
echo $this->Html->script('vue/articleOrders', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Export Docs to order'), 'subtitle' => __('Management')], ['home']);

echo $this->HtmlCustomSite->boxOrder($order);
?>
<section class="">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
            <h3 class="box-title"><?php echo __('List'); ?></h3>
        </div>
        <div class="box-body">
<?php        
echo $this->Form->create(null, ['role' => 'form']);

echo '<div class="row">'; 
echo '<div class="col-md-12">'; 
echo $this->Form->control('print_id', ['type' => 'radio', 'label' => 'Tipologie di stampe', 
            'options' => $exports, 
            'default' => 1,
            'v-model' => "is_cash"
 ]);
echo '</div>';
echo '</div>';

echo $this->Form->submit(__('Print'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

echo $this->Form->end();
?>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>