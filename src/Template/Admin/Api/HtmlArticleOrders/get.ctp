<?php
// debug($results);

echo '<div class="container-fluid">';

echo '<div class="row">';
echo '<div class="col-4">Produttore</div>';
echo '<div class="col-8">'.$results['order']->suppliers_organization->name.'</div>';
echo '</div>';

/*
 gia 'nell' header del modal
echo '<div class="row">';
echo '<div class="col-4">Nome</div>';
echo '<div class="col-8">'.$results['articlesOrder']->name.'</div>';
echo '</div>';
*/

echo '<div class="row">';
echo '<div class="col-4">Prezzo</div>';
echo '<div class="col-8">'.$results['articlesOrder']->prezzo.'</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-4">Pezzi confezione</div>';
echo '<div class="col-8">'.$results['articlesOrder']->pezzi_confezione.'</div>';
echo '</div>';

if($results['articlesOrder']->qta_multipli > 1) {
	echo '<div class="row">';
	echo '<div class="col-4">Multipli</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->qta_multipli.'</div>';
	echo '</div>';
}

if($results['articlesOrder']->qta_minima > 0) {
	echo '<div class="row">';
	echo '<div class="col-4">Quantità minima</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->qta_minima.'</div>';
	echo '</div>';
}

if($results['articlesOrder']->qta_massima > 0) {
	echo '<div class="row">';
	echo '<div class="col-4">Quantità massima</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->qta_massima.'</div>';
	echo '</div>';
}

if($results['articlesOrder']->qta_minima_order > 0) {
	echo '<div class="row">';
	echo '<div class="col-4">Quantità minima rispetto all\'ordine</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->qta_minima_order.'</div>';
	echo '</div>';
}

if($results['articlesOrder']->qta_massima_order > 0) {
	echo '<div class="row">';
	echo '<div class="col-4">Quantità massima rispetto all\'ordine</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->qta_massima_order.'</div>';
	echo '</div>';
}

if($results['articlesOrder']->stato != 'Y') {
	echo '<div class="row">';
	echo '<div class="col-4">Stato</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->stato.'</div>';
	echo '</div>';
}

if(!empty($results['articlesOrder']->codice)) {
	echo '<div class="row">';
	echo '<div class="col-4">Codice</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->codice.'</div>';
	echo '</div>';
}

if(!empty($results['articlesOrder']->nota)) {
	echo '<div class="row">';
	echo '<div class="col-4">Nota</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->nota.'</div>';
	echo '</div>';
}

if(!empty($results['articlesOrder']->ingredienti)) {
	echo '<div class="row">';
	echo '<div class="col-4">Ingredienti</div>';
	echo '<div class="col-8">'.$results['articlesOrder']->ingredienti.'</div>';
	echo '</div>';
}

echo '</div>';