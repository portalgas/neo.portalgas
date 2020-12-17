<?php
// debug($results['articlesOrder']);
// debug($results);
echo '<div class="container-fluid">';

echo $this->HtmlCustomSite->boxSupplierOrganization($results['order']->suppliers_organization);
// echo $this->HtmlCustomSite->boxOrder($results['order']);

if(!empty($results['articlesOrder']['img1'])) {
	echo '<div class="row">';
	echo '<div class="col-4 col-label"></div>';
	echo '<div class="col-8">';
	echo '<span class="box-img"><img src="'.$results['articlesOrder']['img1'].'" width="'.$results['articlesOrder']['img1_width'].'" class="img-article" /></span>';
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
if(isset($results['order']->suppliers_organization->suppliers_organizations_referents)) {
	
	echo '<div class="row">';
	echo '<div class="col-4 col-label">Referenti</div>';
	echo '<div class="col-8">';

	echo '<ul>';
	foreach ($results['order']->suppliers_organization->suppliers_organizations_referents as $referent) {
	    
	    echo '<li>';
	    if($referent->type!='REFERENTE')
		    echo '('.strtolower($referent->type).') ';
	    echo $referent->user->name.' ';
	    if(!empty($referent->user->email))
	    	echo $this->HtmlCustom->mail($referent->user->email);	
	    // debug($referent->user->user_profiles);
	    foreach ($referent->user->user_profiles as $user_profile) {
	    	if($user_profile->profile_key=='profile.phone' && $user_profile->profile_value!='')
                echo ' - '.$user_profile->profile_value.' - '; 
	    	if($user_profile->profile_key=='profile.satispay' && $user_profile->profile_value=='Y')
                echo '<img src="img/satispay-ico.png" title="il referente ha Satispy" />'; 
	    	if($user_profile->profile_key=='profile.satispay_phone' && $user_profile->profile_value=='Y')
                echo ' - '.$user_profile->profile_value.' - '; 
	    }
	    echo '</li>';
	}
	echo '</ul>';

	echo '</div>';
	echo '</div>';
	echo '</div>';
}

echo '</div>';