<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KOrganizationsPay $organizationsPay
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Organizations Pay
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
  </section>

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
          <?php echo $this->Form->create($organizationsPay, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo '<div class="row">';
                echo '<div class="col-md-9">';               
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo '</div>'; 
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('year');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('tot_users');
                echo '</div>'; 
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('tot_orders');
                echo '</div>'; 
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('tot_suppliers_organizations');
                echo '</div>'; 
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('tot_articles');
                echo '</div>'; 
                echo '</div>'; 

                echo '<div class="row">';
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('importo');
                echo '</div>';                 
                echo '<div class="col-md-3">'; 
                echo $this->HtmlCustom->datepicker('data_pay', ['autocomplete' => 'off']);
                echo '</div>'; 
                echo '<div class="col-md-3">'; 
                echo $this->Form->control('beneficiario_pay', ['options' => $beneficiario_pays]);  
                echo '</div>'; 
                echo '<div class="col-md-3">';               
                echo $this->Form->control('type_pay', ['options' => $type_pays]);
                echo '</div>'; 
                echo '</div>'; 
              ?>
            </div>
            <!-- /.box-body -->

          <?php echo $this->Form->submit(__('Submit')); ?>

          <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
