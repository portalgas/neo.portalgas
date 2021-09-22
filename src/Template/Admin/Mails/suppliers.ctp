<?php
// debug($results);
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => "Mail", 'subtitle' => 'Suppliers']);

echo '<section class="content">';

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->element('msg', ['msg' => $mail_send_label]);
echo '</div>';
echo '</div>'; // row

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

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('is_log', ['type' => 'radio', 'label' => 'Scrivi sul log mail.log', 'options' => $is_logs, 'default' => $is_log]);
echo '</div>';
echo '</div>'; // row

echo '<div class="row">';
echo '<div class="col-md-6">';
echo $this->Form->control('mail_test', ['label' => 'Mail da utilizzare per i test']);
echo '</div>';
echo '<div class="col-md-6">';
$msg = "Se valorizzo il campo [$mail_test] escludo i produttori selezionati";
echo $this->element('msg', ['msg' => $msg]);
echo '</div>';
echo '</div>'; // row

echo '<div class="row">';
echo '<div class="col-md-6">';
echo $this->Form->control('organization_prod_gas_id', ['label' => 'Produtti che gestiscono il proprio listino', 'options' => $listOrganizationsProdGas, 'multiple' => true, 'size' => 10]);
echo '</div>';
echo '<div class="col-md-6">';
echo $this->Form->control('supplier_id', ['label' => 'Tutti i produttori', 'options' => $listSuppliers, 'multiple' => true, 'size' => 10]);
echo '</div>';
echo '</div>'; // row
echo $this->Form->control('mail_subject', ['type' => 'text', 'value' => $mail_subject]);
echo $this->Form->control('mail_body', ['type' => 'textarea', 'value' => $mail_body]);
echo '</fieldset>';
echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right', '@click' => 'submit($event);']);
echo $this->Form->end(); 
echo '</div>';
echo '</div>';
echo '</div>';  // row
echo '</section>';
