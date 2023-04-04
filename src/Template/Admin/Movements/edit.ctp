<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __('Movement'), 'subtitle' => __('Edit')], ['home', 'list']);
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id, $user);
// debug($htmlCustomSiteOrders);

echo $this->Html->script('movements', ['block' => 'scriptPageInclude']);
?>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Form'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create($movement, ['id' => 'frm', 'role' => 'form']);
          echo '<div class="box-body">';
          
          echo '<div class="row">';
          echo '<div class="col-md-12">';
          echo $this->Form->control('year');
          echo '</div>';
          echo '</div>'; // row

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          /*
           * se importato a CASSA o PAGAMENTO FATTURA non e' modificabile
           */
          $opts = [];
          if(!$movement->movement_type_edit)
              $opts = ['disabled' => 'disabled'];
          echo $this->Form->control('movement_type_id', ['options' => $movementTypes, $opts]);
          echo '</div>';
          echo '<div class="col-md-6">';

          echo '<div id="box-users">';
          echo $this->Form->control('user_id', ['options' => $users, 'class' => 'select2 form-control']);
          echo '</div>';

          /*
          * produttore
          */
          echo '<div id="box-suppliers">';
          $options = [];
          $options['label'] = '&nbsp;'; 
          echo $htmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations, $options);
          echo '</div>';

          echo '</div>';
          echo '</div>'; // row
            
          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('name');
          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('importo');
          echo '</div>';
          echo '</div>'; // row
          
          echo '<div class="row">';
          echo '<div class="col-md-12">';
          echo $this->Form->control('descri');
          echo '</div>';
          echo '</div>'; // row
          
          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('date');
          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->radio('payment_type', $payment_types);
          echo '</div>';
          echo '</div>'; // row
                  
          echo $this->Form->button(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
          echo '</div>'; /* .box-body */
          echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
