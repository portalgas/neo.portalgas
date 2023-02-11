<?php
use Cake\Core\Configure;
echo $this->Html->script('vue/gasGroupsDeliveriesForm', ['block' => 'scriptPageInclude']);
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?php echo __('Gas Group Delivery'); ?>
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build('/'); ?>"><i class="fa fa-home"></i> <?php echo __('Home'); ?></a></li>
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-list"></i> <?php echo __('List'); ?></a></li>
    </ol>
  </section>

  <!-- Main content -->
  <div id="vue-gas-groups_deliveries-form">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Dati consegna per gruppi del G.A.S.'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create($gasGroupDelivery, ['role' => 'form']);
          echo '<div class="box-body">';


 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('gas_group_id', ['options' => $gasGroups, 'required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('luogo', ['required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->HtmlCustom->datepicker('data', ['autocomplete' => 'off', 'required' => 'required']);
echo '</div>';
echo '</div>'; // row
 
echo '<div class="row">';
echo '<div class="col-md-6">';
echo $this->Form->control('orario_da', ['type' => 'time', 'interval' => 15, 'class' => 'form-control', 'required' => 'required']);
echo '</div>';
echo '<div class="col-md-6">';
echo $this->Form->control('orario_a', ['type' => 'time', 'interval' => 15, 'class' => 'form-control', 'required' => 'required']);
echo '</div>';
echo '</div>'; // row

echo '<div class="row">';
echo '<div class="col-md-12">';
echo $this->Form->control('nota_evidenza', ['label' => 'Maggior informazioni sulla consegna', 'options' => $nota_evidenzas, 'v-model' => 'nota_evidenza_selected']);
echo '</div>';
echo '</div>'; // row

echo '<div class="row" v-if="is_nota_evidenza">';
echo '<div class="col-md-12">';
echo $this->Form->control('nota', ['type' => 'textarea', 'v-model' => 'nota_evidenza']);

echo '<div :class="\'alert-dismissible alert alert-\'+nota_evidenza_css" role="alert" v-if="nota_evidenza!=\'\' && nota_evidenza_css!=\'\'"
            v-html="$options.filters.html(nota_evidenza)">';
echo '</div>';

echo '</div>';
echo '</div>'; // row

echo $this->Form->button(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
echo '</div>'; /* .box-body */
echo $this->Form->end(); 
echo '</div>'; // <!-- /.box -->
echo '</div>';
echo '</div>'; // <!-- /.row -->
echo '</section>';
echo '</div>'; // id="vue-gas-groups_deliveries-form 
