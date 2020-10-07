<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\KOrganization $kOrganization
 */
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      K Organization
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
          <?php echo $this->Form->create($kOrganization, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('name');
                echo $this->Form->control('descrizione');
                echo $this->Form->control('indirizzo');
                echo $this->Form->control('localita');
                echo $this->Form->control('cap');
                echo $this->Form->control('provincia');
                echo $this->Form->control('telefono');
                echo $this->Form->control('telefono2');
                echo $this->Form->control('mail');
                echo $this->Form->control('www');
                echo $this->Form->control('www2');
                echo $this->Form->control('sede_logistica_1');
                echo $this->Form->control('sede_logistica_2');
                echo $this->Form->control('sede_logistica_3');
                echo $this->Form->control('sede_logistica_4');
                echo $this->Form->control('cf');
                echo $this->Form->control('piva');
                echo $this->Form->control('banca');
                echo $this->Form->control('banca_iban');
                echo $this->Form->control('lat');
                echo $this->Form->control('lng');
                echo $this->Form->control('img1');
                echo $this->Form->control('template_id', ['options' => $templates]);
                echo $this->Form->control('j_group_registred');
                echo $this->Form->control('j_page_category_id');
                echo $this->Form->control('j_seo');
                echo $this->Form->control('gcalendar_id');
                echo $this->Form->control('type');
                echo $this->Form->control('paramsConfig');
                echo $this->Form->control('paramsFields');
                echo $this->Form->control('paramsPay');
                echo $this->Form->control('hasMsg');
                echo $this->Form->control('msgText');
                echo $this->Form->control('stato');
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
