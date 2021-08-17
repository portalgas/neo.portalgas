<?php
// debug($results);
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => "Mail", 'subtitle' => 'Suppliers']);

echo '<section class="content">';
echo '<div class="row">';
echo '<div class="col-md-12">';
echo '<div class="box box-primary">';
echo '<div class="box-header with-border">';
echo '<h3 class="box-title">'.__('Form').'</h3>';
echo '</div>';
echo $this->Form->create(null, ['role' => 'form']); 
echo '<fieldset>';
echo '<legend></legend>';
echo '<div class="box-body">'; 
echo $this->Form->control('supplier_id', ['options' => $suppliers, 'empty' => Configure::read('HtmlOptionEmpty')]);
echo $this->Form->control('mail_subject', ['type' => 'text']);
echo $this->Form->control('mail_body', ['type' => 'textarea']);
echo '</fieldset>';
echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submit($event);']);
echo $this->Form->end(); 
echo '</div>';
echo '</div>';
echo '</div>';
echo '</section>';
