<section class="content-header">
  <h1>
    J Content
    <small><?php echo __('View'); ?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-info"></i>
          <h3 class="box-title"><?php echo __('Information'); ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <dl class="dl-horizontal">
            <dt scope="row"><?= __('Title') ?></dt>
            <dd><?= h($jContent->title) ?></dd>
            <dt scope="row"><?= __('Alias') ?></dt>
            <dd><?= h($jContent->alias) ?></dd>
            <dt scope="row"><?= __('Title Alias') ?></dt>
            <dd><?= h($jContent->title_alias) ?></dd>
            <dt scope="row"><?= __('Created By Alias') ?></dt>
            <dd><?= h($jContent->created_by_alias) ?></dd>
            <dt scope="row"><?= __('Attribs') ?></dt>
            <dd><?= h($jContent->attribs) ?></dd>
            <dt scope="row"><?= __('Language') ?></dt>
            <dd><?= h($jContent->language) ?></dd>
            <dt scope="row"><?= __('Xreference') ?></dt>
            <dd><?= h($jContent->xreference) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($jContent->id) ?></dd>
            <dt scope="row"><?= __('Asset Id') ?></dt>
            <dd><?= $this->Number->format($jContent->asset_id) ?></dd>
            <dt scope="row"><?= __('State') ?></dt>
            <dd><?= $this->Number->format($jContent->state) ?></dd>
            <dt scope="row"><?= __('Sectionid') ?></dt>
            <dd><?= $this->Number->format($jContent->sectionid) ?></dd>
            <dt scope="row"><?= __('Mask') ?></dt>
            <dd><?= $this->Number->format($jContent->mask) ?></dd>
            <dt scope="row"><?= __('Catid') ?></dt>
            <dd><?= $this->Number->format($jContent->catid) ?></dd>
            <dt scope="row"><?= __('Created By') ?></dt>
            <dd><?= $this->Number->format($jContent->created_by) ?></dd>
            <dt scope="row"><?= __('Modified By') ?></dt>
            <dd><?= $this->Number->format($jContent->modified_by) ?></dd>
            <dt scope="row"><?= __('Checked Out') ?></dt>
            <dd><?= $this->Number->format($jContent->checked_out) ?></dd>
            <dt scope="row"><?= __('Version') ?></dt>
            <dd><?= $this->Number->format($jContent->version) ?></dd>
            <dt scope="row"><?= __('Parentid') ?></dt>
            <dd><?= $this->Number->format($jContent->parentid) ?></dd>
            <dt scope="row"><?= __('Ordering') ?></dt>
            <dd><?= $this->Number->format($jContent->ordering) ?></dd>
            <dt scope="row"><?= __('Access') ?></dt>
            <dd><?= $this->Number->format($jContent->access) ?></dd>
            <dt scope="row"><?= __('Hits') ?></dt>
            <dd><?= $this->Number->format($jContent->hits) ?></dd>
            <dt scope="row"><?= __('Featured') ?></dt>
            <dd><?= $this->Number->format($jContent->featured) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($jContent->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($jContent->modified) ?></dd>
            <dt scope="row"><?= __('Checked Out Time') ?></dt>
            <dd><?= h($jContent->checked_out_time) ?></dd>
            <dt scope="row"><?= __('Publish Up') ?></dt>
            <dd><?= h($jContent->publish_up) ?></dd>
            <dt scope="row"><?= __('Publish Down') ?></dt>
            <dd><?= h($jContent->publish_down) ?></dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Introtext') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->introtext); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Fulltext') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->fulltext); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Images') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->images); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Urls') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->urls); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Metakey') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->metakey); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Metadesc') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->metadesc); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width"></i>
          <h3 class="box-title"><?= __('Metadata') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($jContent->metadata); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('K Suppliers') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($jContent->k_suppliers)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Category Supplier Id') ?></th>
                    <th scope="col"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Nome') ?></th>
                    <th scope="col"><?= __('Cognome') ?></th>
                    <th scope="col"><?= __('Descrizione') ?></th>
                    <th scope="col"><?= __('Indirizzo') ?></th>
                    <th scope="col"><?= __('Localita') ?></th>
                    <th scope="col"><?= __('Cap') ?></th>
                    <th scope="col"><?= __('Provincia') ?></th>
                    <th scope="col"><?= __('Lat') ?></th>
                    <th scope="col"><?= __('Lng') ?></th>
                    <th scope="col"><?= __('Telefono') ?></th>
                    <th scope="col"><?= __('Telefono2') ?></th>
                    <th scope="col"><?= __('Fax') ?></th>
                    <th scope="col"><?= __('Mail') ?></th>
                    <th scope="col"><?= __('Www') ?></th>
                    <th scope="col"><?= __('Nota') ?></th>
                    <th scope="col"><?= __('Cf') ?></th>
                    <th scope="col"><?= __('Piva') ?></th>
                    <th scope="col"><?= __('Conto') ?></th>
                    <th scope="col"><?= __('J Content Id') ?></th>
                    <th scope="col"><?= __('Img1') ?></th>
                    <th scope="col"><?= __('Can Promotions') ?></th>
                    <th scope="col"><?= __('Delivery Type Id') ?></th>
                    <th scope="col"><?= __('Owner Organization Id') ?></th>
                    <th scope="col"><?= __('Stato') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($jContent->k_suppliers as $kSuppliers): ?>
              <tr>
                    <td><?= h($kSuppliers->id) ?></td>
                    <td><?= h($kSuppliers->category_supplier_id) ?></td>
                    <td><?= h($kSuppliers->name) ?></td>
                    <td><?= h($kSuppliers->nome) ?></td>
                    <td><?= h($kSuppliers->cognome) ?></td>
                    <td><?= h($kSuppliers->descrizione) ?></td>
                    <td><?= h($kSuppliers->indirizzo) ?></td>
                    <td><?= h($kSuppliers->localita) ?></td>
                    <td><?= h($kSuppliers->cap) ?></td>
                    <td><?= h($kSuppliers->provincia) ?></td>
                    <td><?= h($kSuppliers->lat) ?></td>
                    <td><?= h($kSuppliers->lng) ?></td>
                    <td><?= h($kSuppliers->telefono) ?></td>
                    <td><?= h($kSuppliers->telefono2) ?></td>
                    <td><?= h($kSuppliers->fax) ?></td>
                    <td><?= h($kSuppliers->mail) ?></td>
                    <td><?= h($kSuppliers->www) ?></td>
                    <td><?= h($kSuppliers->nota) ?></td>
                    <td><?= h($kSuppliers->cf) ?></td>
                    <td><?= h($kSuppliers->piva) ?></td>
                    <td><?= h($kSuppliers->conto) ?></td>
                    <td><?= h($kSuppliers->j_content_id) ?></td>
                    <td><?= h($kSuppliers->img1) ?></td>
                    <td><?= h($kSuppliers->can_promotions) ?></td>
                    <td><?= h($kSuppliers->delivery_type_id) ?></td>
                    <td><?= h($kSuppliers->owner_organization_id) ?></td>
                    <td><?= h($kSuppliers->stato) ?></td>
                    <td><?= h($kSuppliers->created) ?></td>
                    <td><?= h($kSuppliers->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'KSuppliers', 'action' => 'view', $kSuppliers->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'KSuppliers', 'action' => 'edit', $kSuppliers->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'KSuppliers', 'action' => 'delete', $kSuppliers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $kSuppliers->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
