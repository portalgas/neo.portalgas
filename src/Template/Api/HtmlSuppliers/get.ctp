<?php
use Cake\Core\Configure;

// debug($results);

$config = Configure::read('Config');
$portalgas_app_root = $config['Portalgas.App.root'];
$portalgas_fe_url = $config['Portalgas.fe.url'];

$img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $results->img1);
$img_path_supplier = $portalgas_app_root.$img_path_supplier;

echo '<div class="container-fluid">';
echo '<div class="box-supplier-organization">';
// echo  $results->id;

$url = '';
if(!empty($results->img1) && file_exists($img_path_supplier)) {
    
    $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $results->img1);

    echo '<span class="box-img"><img src="'.$url.'" alt="'.$results->name.'" title="'.$results->name.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" /></span> ';
}
echo '<span class="box-name">';
if(!empty($results->www)) 
    echo '<a href="'.$results->www.'" target="_blank" title="vai al sito del produttore">';
echo  $results->name;
if(!empty($results->www))
    echo '</a>'; 
echo '</span>';
echo  "</div>";

/*
 * contact
 */
echo '<div class="row">';
echo '<div class="col-12">';

echo ' '.$results->telefono;
echo ' '.$results->telefono2;
echo ' '.$results->mail;

echo '</div>';
echo '</div>';

debug($results->lat);
debug($results->lng);

/* 
 * maps
 */
if(!empty($results->lat) && !empty($results->lng)) {
	echo '<div class="row">';
	echo '<div class="col-12">';
	echo '<div id="map" style="width: 100%; height: 500px"></div>';
	echo '</div>';
	echo '</div>';
}

/* 
 * content
 */
if(isset($results->content) && !empty($results->content)) {
	echo '<div class="row">';
	echo '<div class="col-12">';

	echo $results->content->introtext;
	echo str_replace('{flike}', '', $results->content->fulltext);

	echo '</div>';
	echo '</div>';
}

/*
 * suppliers_organizations
 */
foreach($results->suppliers_organizations as $suppliers_organization) {

	$organization = $suppliers_organization->organization;
	$img_path_organization = sprintf(Configure::read('Organization.img.path.full'), $organization->img1);

	echo '<div class="row">';
	echo '<div class="col-4 col-label">G.A.S.</div>';
	echo '<div class="col-8">';

	if(!empty($organization->img1) && file_exists($img_path_organization)) {

	    $url = sprintf($portalgas_fe_url.Configure::read('Organization.img.path.full'), $organization->img1);

	    echo '<span class="box-img"><img src="'.$url.'" alt="'.$organization->name.'" title="'.$organization->name.'" width="'.Configure::read('Organization.img.preview.width').'" class="img-supplier" /></span> ';
	}

	echo $organization->name;
	echo ' '.$organization->indirizzo;
	echo ' '.$organization->localita;
	echo ' '.$organization->provincia;
	echo ' '.$organization->img;
	echo '</div>';
	echo '</div>';
}

echo '</div>'; // container-fluid

echo $js;
?>
<style>
</style>