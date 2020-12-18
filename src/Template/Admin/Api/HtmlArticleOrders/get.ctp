<?php
// debug($results['articlesOrder']);
// debug($results);
echo '<div class="container-fluid">';

echo $this->HtmlCustomSite->boxSupplierOrganization($results['order']->suppliers_organization);
// echo $this->HtmlCustomSite->boxOrder($results['order']);

if(!empty($results['articlesOrder']['is_bio']) || !empty($results['articlesOrder']['img1'])) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">';
	if(!empty($results['articlesOrder']['is_bio']))
		echo '<span class="box-bio"><img class="responsive" src="/img/is-bio.png" alt="Agricoltura Biologica" title="Agricoltura Biologica"></span>';
	echo '</div>';
	echo '<div class="col-8">';
	if(!empty($results['articlesOrder']['img1']))
		echo '<span class="box-img"><img src="'.$results['articlesOrder']['img1'].'" class="img-article" /></span>';
	echo '</div>';
	echo '</div>';	
}

           
if(!empty($results['order']->suppliers_organization->frequenza)) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Frequenza</div>';
	echo '<div class="col-8">'.$results['order']->suppliers_organization->frequenza.'</div>';
	echo '</div>';	
}


if(!empty($results['articlesOrder']['codice'])) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Codice</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['codice'].'</div>';
	echo '</div>';
}

/*
 gia 'nell' header del modal
echo '<div class="row">';
echo '<div class="col-4 col-label">Nome</div>';
echo '<div class="col-8">'.$results['articlesOrder']->name.'</div>';
echo '</div>';
*/

echo '<div class="row">';
echo '<div class="col-4 col-label">Prezzo</div>';
echo '<div class="col-8">'.$this->HtmlCustom->importo($results['articlesOrder']['price']).'</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-4 col-label">Pezzi confezione</div>';
echo '<div class="col-8">'.$results['articlesOrder']['conf'].'</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-4 col-label">Unità di misura di riferimento</div>';
echo '<div class="col-8">'.$results['articlesOrder']['um_rif_label'].'</div>';
echo '</div>';

if($results['articlesOrder']['qta_multipli'] > 1) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Multipli</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['qta_multipli'].'</div>';
	echo '</div>';
}

if($results['articlesOrder']['qta_minima'] > 0) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Quantità minima</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['qta_minima'].'</div>';
	echo '</div>';
}

if($results['articlesOrder']['qta_massima'] > 0) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Quantità massima</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['qta_massima'].'</div>';
	echo '</div>';
}

if($results['articlesOrder']['qta_minima_order'] > 0) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Quantità minima rispetto all\'ordine</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['qta_minima_order'].'</div>';
	echo '</div>';
}

if($results['articlesOrder']['qta_massima_order'] > 0) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Quantità massima rispetto all\'ordine</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['qta_massima_order'].'</div>';
	echo '</div>';
}

if($results['articlesOrder']['stato'] != 'Y') {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Stato</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['stato'].'</div>';
	echo '</div>';
}

if(!empty($results['articlesOrder']['descri'])) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Nota</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['descri'].'</div>';
	echo '</div>';
}

if(!empty($results['articlesOrder']['ingredients'])) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Ingredienti</div>';
	echo '<div class="col-8">'.$results['articlesOrder']['ingredients'].'</div>';
	echo '</div>';
}

/*
 * R E F E R E N T I 
 */ 
if(isset($results['order']->referents)) {
	
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Referenti</div>';
	echo '<div class="col-8">';
	echo $this->HtmlCustomSite->boxVerticalSupplierOrganizationreferents($results['order']->referents);
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

echo '</div>';