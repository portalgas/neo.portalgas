  <?php
use Cake\Core\Configure;

echo $this->Html->script('vue/testAjax', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => "Test ajax service", 'subtitle' => '']);

echo '<div id="vue-test-ajax">';
echo '<div style="text-align: center;" class="run run-spinner"><div class="spinner"></div></div>';
?>
<section class="content-header">
    <h1>
      Price Type
      <small><?php echo __('Tests ajax'); ?></small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Tests ajax'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create(null, ['role' => 'form']); 
          echo '<fieldset>';
          echo '<legend></legend>';
          ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('service_url', ['options' => $service_urls, 'empty' => Configure::read('HtmlOptionEmpty')]);
                echo $this->Form->control('delivery_id', ['options' => $deliveries, 'empty' => Configure::read('HtmlOptionEmpty')]);
                echo $this->Form->control('order_id', ['label' => 'Orders OPEN / PROCESSED-BEFORE-DELIVERY', 'options' => $orders, 'empty' => Configure::read('HtmlOptionEmpty')]);
              ?>
            </div>
            <!-- /.box-body -->

          <?php 
          echo '</fieldset>';
          echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submit($event);']);
          echo $this->Form->end(); 
          ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>

<?php
echo '<div v-if="esito!=null" style="text-align: center;">{{ esito }}</div>';

echo '</div>'; // end id="vue-test-ajax"

