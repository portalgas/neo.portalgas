<?php
use Cake\Core\Configure;

//echo $this->Html->script('vue/orders', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/suppliersOrganization', ['block' => 'scriptPageInclude']);

/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($scope);
// debug($htmlCustomSiteOrders);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    ArticlesOrders
  </h1>
</section>

<?php
/*
 * produttore
 */
$suppliersOrganizations = [];
$suppliersOrganizations[$order->suppliers_organization->id] = $order->suppliers_organization->name;

echo '<div class="row">';
echo '<div class="col-md-8">';
echo $this->{$htmlCustomSiteOrders}->supplierOrganizations($suppliersOrganizations);
echo '</div>';
echo '<div class="col-md-4" id="vue-supplier-organization" style="display: none;">';
echo '<div class="box-img" v-if="supplier_organization.supplier.img1!=\'\'"><img width="'.Configure::read('Supplier.img.preview.width').'" class="img-responsive-disabled userAvatar" v-bind:src="supplier_organization.img1" /></div>';
echo '<div class="box-name">{{supplier_organization.name}}</div>';
echo '<div class="box-owner">'.__('organization_owner_articles').': {{supplier_organization.owner_articles | ownerArticlesLabel}}</div>';
echo '</div>';
echo '</div>';

echo $this->HtmlCustomSite->boxOrder($order);
// debug($order);
?>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('supplier_organization_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('category_article_id') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('codice') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('prezzo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('um') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('um_riferimento') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('pezzi_confezione') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_minima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_minima_order') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_massima_order') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('qta_multipli') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('alert_to_qta') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('bio') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('img1') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('stato') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('flag_presente_articlesorders') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($articles as $article): ?>
                <tr>
                  <td><?= $this->Number->format($article->id) ?></td>
                  <td><?= $article->has('organization') ? $this->Html->link($article->organization->name, ['controller' => 'Organizations', 'action' => 'view', $article->organization->id]) : '' ?></td>
                  <td><?= $article->has('suppliers_organization') ? $this->Html->link($article->suppliers_organization->name, ['controller' => 'SuppliersOrganizations', 'action' => 'view', $article->suppliers_organization->id]) : '' ?></td>
                  <td><?= $article->has('categories_article') ? $this->Html->link($article->categories_article->name, ['controller' => 'CategoriesArticles', 'action' => 'view', $article->categories_article->id]) : '' ?></td>
                  <td><?= h($article->name) ?></td>
                  <td><?= h($article->codice) ?></td>
                  <td><?= $this->Number->format($article->prezzo) ?></td>
                  <td><?= $this->Number->format($article->qta) ?></td>
                  <td><?= h($article->um) ?></td>
                  <td><?= h($article->um_riferimento) ?></td>
                  <td><?= $this->Number->format($article->pezzi_confezione) ?></td>
                  <td><?= $this->Number->format($article->qta_minima) ?></td>
                  <td><?= $this->Number->format($article->qta_massima) ?></td>
                  <td><?= $this->Number->format($article->qta_minima_order) ?></td>
                  <td><?= $this->Number->format($article->qta_massima_order) ?></td>
                  <td><?= $this->Number->format($article->qta_multipli) ?></td>
                  <td><?= $this->Number->format($article->alert_to_qta) ?></td>
                  <td><?= h($article->bio) ?></td>
                  <td><?= h($article->img1) ?></td>
                  <td><?= h($article->stato) ?></td>
                  <td><?= h($article->flag_presente_articlesorders) ?></td>
                  <td><?= h($article->created) ?></td>
                  <td><?= h($article->modified) ?></td>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $article->id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $article->id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>