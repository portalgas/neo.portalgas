<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/testAjax', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => "Price Type", 'subtitle' => 'Test ajax']);

echo '<div id="vue-test-ajax">';
echo '<div style="text-align: center;" class="run run-spinner"><div class="spinner"></div></div>';

  echo '<section class="content">';
  echo '<div class="row">';
  echo '<div class="col-md-12">';
  echo '<div class="box box-primary">';
  echo '<div class="box-header with-border">';
  echo '<h3 class="box-title">'.__('Filter service').'</h3>';
  echo '</div>';
  echo $this->Form->create(null, ['role' => 'form']); 
  echo '<fieldset>';
  echo '<legend></legend>';
  echo '<div class="box-body">'; 
  echo $this->Form->control('service_url', ['options' => $service_urls, 'empty' => Configure::read('HtmlOptionEmpty')]);


  echo '<div class="row">';
  echo '<div class="col-md-6">';  
  echo $this->Form->control('value1', ['value' => $value1]);
  echo '</div>';
  echo '<div class="col-md-6">';  
  echo $this->Form->control('param1');
  echo '</div>';
  echo '</div>';

  echo '</fieldset>';
  echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submitService($event);']);
  echo $this->Form->end(); 
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</section>';

  if(!empty($service_url)) {

  } // end if(!empty($service_url)) 