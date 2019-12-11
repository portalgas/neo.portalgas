<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
            <span data-toggle="tooltip" title="<?php echo $totResults;?>" class="badge bg-light-blue"><?php echo $totResults;?></span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Customer']) ?></legend>
    <?php
    echo '<div class="row-">';
    echo '<div class="col-md-12">';
    echo $this->Form->control('search_name', ['value' => $search_name]);
    echo '</div>';
    echo '</div>';

    echo '<div class="row-">';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_geo_region_id', ['options' => $geoRegions, 'class' => 'form-control select2','empty' => [0 => __('FrmListEmpty')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_geo_province_id', ['options' => false, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListAttend')]]);
    echo '</div>';
    echo '<div class="col-md-4">';    
    echo $this->Form->control('search_geo_comune_id', ['options' => false, 'class' => 'form-control select2', 'empty' => [0 => __('FrmListAttend')]]);
    echo '</div>';
    echo '</div>';
    ?>
</fieldset>
</div>
    <div class="box-footer">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>
</div>
<?= $this->Form->end() ?>
<?php
$this->Geo->draw($jsonGeoProvinces, $jsonGeoComunes, $search_geo_region_id, $search_geo_province_id, $search_geo_comune_id);

?>