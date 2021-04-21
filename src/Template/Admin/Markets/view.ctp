<section class="content-header">
  <h1>
    Market
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
            <dt scope="row"><?= __('Organization') ?></dt>
            <dd><?= $market->has('organization') ? $this->Html->link($market->organization->name, ['controller' => 'Organizations', 'action' => 'view', $market->organization->id]) : '' ?></dd>
            <dt scope="row"><?= __('Name') ?></dt>
            <dd><?= h($market->name) ?></dd>
            <dt scope="row"><?= __('Img1') ?></dt>
            <dd><?= h($market->img1) ?></dd>
            <dt scope="row"><?= __('State Code') ?></dt>
            <dd><?= h($market->state_code) ?></dd>
            <dt scope="row"><?= __('Is System') ?></dt>
            <dd><?= h($market->is_system) ?></dd>
            <dt scope="row"><?= __('Id') ?></dt>
            <dd><?= $this->Number->format($market->id) ?></dd>
            <dt scope="row"><?= __('Sort') ?></dt>
            <dd><?= $this->Number->format($market->sort) ?></dd>
            <dt scope="row"><?= __('Data Inizio') ?></dt>
            <dd><?= h($market->data_inizio) ?></dd>
            <dt scope="row"><?= __('Data Fine') ?></dt>
            <dd><?= h($market->data_fine) ?></dd>
            <dt scope="row"><?= __('Created') ?></dt>
            <dd><?= h($market->created) ?></dd>
            <dt scope="row"><?= __('Modified') ?></dt>
            <dd><?= h($market->modified) ?></dd>
            <dt scope="row"><?= __('Is Active') ?></dt>
            <dd><?= $market->is_active ? __('Yes') : __('No'); ?></dd>
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
          <h3 class="box-title"><?= __('Nota') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <?= $this->Text->autoParagraph($market->nota); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-share-alt"></i>
          <h3 class="box-title"><?= __('Market Articles') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <?php if (!empty($market->market_articles)): ?>
          <table class="table table-hover">
              <tr>
                    <th scope="col"><?= __('Id') ?></th>
                    <th scope="col"><?= __('Organization Id') ?></th>
                    <th scope="col"><?= __('Market Id') ?></th>
                    <th scope="col"><?= __('Article Id') ?></th>
                    <th scope="col"><?= __('Created') ?></th>
                    <th scope="col"><?= __('Modified') ?></th>
                    <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
              <?php foreach ($market->market_articles as $marketArticles): ?>
              <tr>
                    <td><?= h($marketArticles->id) ?></td>
                    <td><?= h($marketArticles->organization_id) ?></td>
                    <td><?= h($marketArticles->market_id) ?></td>
                    <td><?= h($marketArticles->article_id) ?></td>
                    <td><?= h($marketArticles->created) ?></td>
                    <td><?= h($marketArticles->modified) ?></td>
                      <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['controller' => 'MarketArticles', 'action' => 'view', $marketArticles->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['controller' => 'MarketArticles', 'action' => 'edit', $marketArticles->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['controller' => 'MarketArticles', 'action' => 'delete', $marketArticles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $marketArticles->id), 'class'=>'btn btn-danger btn-xs']) ?>
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
