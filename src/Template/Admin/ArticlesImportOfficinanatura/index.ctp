  <section class="content-header">
    <h1>
      Officina Naturae
      <small><?php echo __('CSV'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
  </section>

  <?php 
  $msg = '';
  $msg .= "Colonne:<br/>CODICE ARTICOLI | DESCRIZIONE | QUANTITA' | IMPORTO FINALE<br />";
  $msg .= "<ul>";
  $msg .= "<li>eliminare prime righe intestazione</li>";
  $msg .= "<li>eliminare ultime righe dati fatturazione</li>";
  $msg .= "<li>eliminare eventuali *</li>";
  $msg .= "</ul>";
  
  echo $this->element('msg', ['msg' => $msg]);
  ?>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Import File CSV'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create(null, ['role' => 'form', 'type' => 'file']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('file', ['type' => 'file']);
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
