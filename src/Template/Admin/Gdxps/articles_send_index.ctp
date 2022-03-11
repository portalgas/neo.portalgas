<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

echo $this->HtmlCustomSite->boxTitle(['title' => __('Article-Send'), 'subtitle' => 'Il tuo listino articoli sarà trasmesso al sito https://www.economiasolidale.net così sarà disponibile ad altri gestionali']);

echo '<section>';

echo '<div class="info-box">';
echo '    <span class="info-box-icon"><i class="fa fa-info"></i></span>';

echo '<div class="info-box-content">';
echo '<span class="info-box-text">';
if(!empty($supplier_organization->supplier->img1)) {

    $url = $portalgas_fe_url.Configure::read('Supplier.img.path.full');

    $img1_path = sprintf($url, $supplier_organization->supplier->img1);
    echo '<img src="'.$img1_path.'" width="'.Configure::read('Supplier.img.preview.width').'" /> ';
}
echo $supplier_organization->name;
echo '</span>';
if(empty($supplier_organization->supplier->piva)) {
    echo '<p>';
    echo $this->element('msg', ['msg' => __('MsgSupplierSendVatNumberRequired'), 'class' => 'danger']);
    echo '</p>';
}
else {
    echo '<p>';
    echo '<span class="info-box-number">'.__('Supplier-VatNumber').': '.$supplier_organization->supplier->piva.'</span>';
    echo '</p>';
}
echo '</div>';
echo '</div>';


if(empty($articles) || $articles->count()==0)
    echo $this->element('msgResults', ['action_add' => false]);
else {
?>
<div class="table-responsive">
<table class="table table-striped table-hover table-condensed" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
                <th colspan="2"><?= __('Name') ?></th>
                <th><?= __('Codice') ?></th>
				<?php
                /* 
                <th><?= __('Supplier Organization') ?></th>
                <th><?= __('owner_supplier_organization_id') ?></th>
				<th><?= __('owner_organization_id') ?></th>
				<th><?= __('owner_organization_name') ?></th>
                */ ?>
                <th><?= __('Bio') ?></th>
                <th><?= __('Prezzo') ?></th>
                <th><?= __('Qta') ?></th>
                <th><?= __('Conf') ?></th>
                <th><?= __('PrezzoUnita') ?></th>
                <?php 
                /* 
                <th><?= __('um_riferimento') ?></th>
                <th><?= __('qta_minima') ?></th>
                <th><?= __('qta_massima') ?></th>
                <th><?= __('qta_minima_order') ?></th>
                <th><?= __('qta_massima_order') ?></th>
                <th><?= __('qta_multipli') ?></th>
                <th><?= __('alert_to_qta') ?></th>
                <th><?= __('stato') ?></th>
                <th><?= __('flag_presente_articlesorders') ?></th>
                <th class="actions"><?= __('Actions') ?></th> */
                ?>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
            <tr>
                <?php  
                echo '<td>';
                if(!empty($article->img1)) {
                    $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

                    $img1_path = sprintf($url, $article->organization_id, $article->img1);
                    echo '<img src="'.$img1_path.'" width="'.Configure::read('Article.img.preview.width').'" />';
                }
                echo '</td>';
                echo '<td>'.h($article->name).'</td>';
                echo '<td>'.h($article->codice).'</td>';

                /*
                echo '<td>'.$article->suppliers_organization->name.'</td>';
                echo '<td>'.$article->suppliers_organization->supplier->img1.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_organization_id.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_supplier_organization_id.'</td>';
                echo '<td>'.$article->suppliers_organization->owner_organization->id.'</td>';
                */
                ?>
                <td><?= h($article->bio) ?></td>
                <td><?= $this->Number->format($article->prezzo) ?></td>
                <td><?= $this->Number->format($article->qta) ?></td>
                <td><?= $this->Number->format($article->pezzi_confezione) ?></td><td><?= h($article->um) ?></td>
                <?php
                /*
                <td><?= h($article->um_riferimento) ?></td>
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
                */
                ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>    
<?php
} // if(empty($articles) || $articles->count()==0)

if($canSend) {
    echo '<div style="display: none;" class="msg-send alert alert-info"></div>';
    
    echo '<div style="display: none;text-align: center;" class="run run-send">';
    echo '<div class="spinner"></div>';
    echo '</div>';

    echo '<div class="btn-send">';
    echo $this->Html->link(__('Send'), [''], ['id' => 'btn-send', 'class' => 'btn btn-primary pull-right']);
    echo '</div>';

}
else {
    echo '<div class="alert alert-danger">Non è valorizzata la <b>partita iva</b>, questo campo è obbligatori per tramettere il tuo listino articoli<br >Contattaci all\'indirizzo mail <a href="mailto:'.Configure::read('SOC.mail-contatti').'">'.Configure::read('SOC.mail-contatti').'</a></div>';
}

echo '</section>';

$js = "
$(function () {

    $('#btn-send').click(function (e) {

        e.preventDefault();

        let ico_spinner = 'fa-lg fa fa-spinner fa-spin';
        let ajaxUrl = '/admin/api/gdpx/send-articles';
    
        $('.btn-send').hide();
        $('.run-send').show();
        $('.run-send .spinner').addClass(ico_spinner);

        $.ajax({url: ajaxUrl, 
           // data: data, 
            type: 'POST',
            dataType: 'json',
            cache: false, 
            headers: {
              'X-CSRF-Token': csrfToken
            },                            
            success: function (response) {
                console.log(response);
                if(response.esito===true)
                    $('.msg-send').html(response.msg);
                else
                    $('.msg-send').html(response.errors);

                return false;
            },
            error: function (e) {
                console.log(e);
                $('.msg-send').html('Errore nel trasmissione!');
                return false;
            },
            complete: function (e) {
                $('.msg-send').show();
                $('.run-send').hide();
                return false;
            }
        });
    });
});
";

$this->Html->scriptBlock($js, ['block' => true]);
?>