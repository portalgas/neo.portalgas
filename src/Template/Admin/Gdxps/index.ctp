<?php
use Cake\Core\Configure; 

$this->start('tb_sidebar');
echo '<li class="sidebar-menu-action">';
$this->Html->link('<i class="fa fa-list"></i> <span>'.__('Article Export').'</span>', ['action' => 'index'], ['escape' => false]);
echo '</li>';
$this->end();

echo $this->element('breadcrumb', ['label' => __('Article Export'), 'action' => __('index')]);

echo $this->Form->create();
echo '<fieldset>';
echo '<legend>'.__('Article Export').'</legend>';

echo '<div class="row">';
echo '<div class="col-md-12">';   
echo $this->Form->control('supplier_organization_id', ['options' => $acl_supplier_organizations, 'class' => 'form-control select2']);
echo '</div>';
echo '</div>';

echo '</fieldset>';
echo $this->Form->button(__('Submit'), ['class' => 'btn btn-primary pull-right']);
echo $this->Form->end();

echo '<div class="clearfix"></div>';

if(empty($articles) || $articles->count()==0)
    echo $this->element('msgResults');
else {
?>
<div class="table-responsive">
<table class="table table-striped table-hover table-condensed" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
                <th colspan="2"><?= __('Supplier Organization') ?></th>
                <th><?= __('owner_articles') ?></th>
				<th><?= __('owner_organization_id') ?></th>
				<th><?= __('owner_supplier_organization_id') ?></th>
				<th><?= __('owner_organization_id') ?></th>
				<th><?= __('owner_organization_name') ?></th>

                <th><?= __('bio') ?></th>
                <th colspan="2"><?= __('name') ?></th>
                <th><?= __('codice') ?></th>
                <th><?= __('prezzo') ?></th>
                <th><?= __('qta') ?></th>
                <th><?= __('um') ?></th>
                <th><?= __('um_riferimento') ?></th>
                <th><?= __('pezzi_confezione') ?></th>
                <th><?= __('qta_minima') ?></th>
                <th><?= __('qta_massima') ?></th>
                <th><?= __('qta_minima_order') ?></th>
                <th><?= __('qta_massima_order') ?></th>
                <th><?= __('qta_multipli') ?></th>
                <th><?= __('alert_to_qta') ?></th>
                <th><?= __('stato') ?></th>
                <th><?= __('flag_presente_articlesorders') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
            <tr>
                <?php 
                echo '<td>'.$article->suppliers_organization->supplier->img1.'</td>';
                echo '<td>'.$article->suppliers_organization->name.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_articles.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_organization_id.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_supplier_organization_id.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_organization->id.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_organization->name.'</td>';
                ?>
                <td><?= h($article->bio) ?></td>
                <td><?= h($article->img1) ?></td>
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
                <td><?= h($article->stato) ?></td>
                <td><?= h($article->flag_presente_articlesorders) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $article->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $article->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $article->id], ['confirm' => __('Are you sure you want to delete # {0}?', $article->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>    
<?php
}
?>