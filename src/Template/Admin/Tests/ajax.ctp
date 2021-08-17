<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => "Price Type", 'subtitle' => 'Test ajax']);

echo '<div id="vue-test-ajax">';
echo '<div style="text-align: center;" class="run run-spinner"><div class="spinner"></div></div>';

  echo '<section class="content">';
  echo '<div class="row">';
  echo '<div class="col-md-12">';
  echo '<div class="box box-primary">';
  echo '<div class="box-header with-border">';
  echo '<h3 class="box-title">'.__('Filter factory order').'</h3>';
  echo '</div>';
  echo $this->Form->create(null, ['role' => 'form']); 
  echo '<fieldset>';
  echo '<legend></legend>';
  echo '<div class="box-body">'; 
  echo $this->Form->control('order_type_id', ['options' => $order_type_ids, 'empty' => Configure::read('HtmlOptionEmpty')]);
  echo '</fieldset>';
  echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submit($event);']);
  echo $this->Form->end(); 
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</section>';

  if(!empty($order_type_id)) {

      echo $this->Html->script('vue/testAjax', ['block' => 'scriptPageInclude']);

      echo '<section class="content">';
      echo '<div class="row">';
      echo '<div class="col-md-12">';
      echo '<div class="box box-primary">';
      echo '<div class="box-header with-border">';
      echo '<h3 class="box-title">'.__('Parameters').'</h3>';
      echo '</div>';
      echo $this->Form->create(null, ['role' => 'form']); 
      echo $this->Form->control('order_type_id', ['type' => 'hidden', 'value' => $order_type_id]);
      echo '<fieldset>';
      echo '<legend></legend>';
      echo '<div class="box-body">';
      echo $this->Form->control('service_url', ['options' => $service_urls, 'empty' => Configure::read('HtmlOptionEmpty')]);

      echo $this->Form->control('supplier_organization_id', ['options' => $suppliersOrganizations, 'empty' => Configure::read('HtmlOptionEmpty')]);

      echo $this->Form->control('delivery_id', ['options' => $deliveries, 'empty' => Configure::read('HtmlOptionEmpty')]);

      echo $this->Form->control('order_id', ['label' => 'Orders OPEN / PROCESSED-BEFORE-DELIVERY', 'options' => $orders, 'empty' => Configure::read('HtmlOptionEmpty')]);
      echo '</div>';
      echo '</fieldset>';
      echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submit($event);']);
      echo $this->Form->end(); 
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</section>';

      /* 
       * results vue
       */
      echo '<div v-if="esito!=null" style="text-align: center;">{{ esito }}</div>';
      echo '</div>'; // end id="vue-test-ajax"

  } // end if(!empty($order_type_id)) 