<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KProdGasPromotionsOrganization $kProdGasPromotionsOrganization
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Prod Gas Promotions Organization
      <small><?php echo __('Edit'); ?></small>
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
          <?php echo $this->Form->create($kProdGasPromotionsOrganization, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('prod_gas_promotion_id', ['options' => $prodGasPromotions]);
                echo $this->Form->control('organization_id', ['options' => $organizations]);
                echo $this->Form->control('order_id', ['options' => $orders]);
                echo $this->Form->control('hasTrasport');
                echo $this->Form->control('trasport');
                echo $this->Form->control('hasCostMore');
                echo $this->Form->control('cost_more');
                echo $this->Form->control('nota_supplier');
                echo $this->Form->control('nota_user');
                echo $this->Form->control('user_id', ['options' => $users]);
                echo $this->Form->control('state_code');
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
