<?php 
/*
 * link menu' su portalas rimappati in /templates/v01/index.php
 */

use Cake\Core\Configure; 

$user = $this->Identity->get();

$i=0;
$menus = [];
$menus[$i]['label'] = 'Home';
$menus[$i]['url'] = $config['Portalgas.fe.url'];
$menus[$i]['target'] = '';

if(!empty($organization)) {
  $i++;
  $menus = [];
  $menus[$i]['label'] = 'Consegne';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/consegne-'.$organization->j_seo;
  $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/consegne-'.$organization->j_seo;
  $menus[$i]['target'] = '';

  if ($user->organization->paramsConfig['hasStoreroom'] == 'Y' && $user->organization->paramsConfig['hasStoreroomFrontEnd'] == 'Y') {
    $i++;
    $menus[$i]['label'] = __('Storeroom');
    // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/dispensa-'.$organization->j_seo;
    $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/dispensa-'.$organization->j_seo;
    $menus[$i]['target'] = '';
  } 
  $i++;
  $menus[$i]['label'] = 'Acquista';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/fai-la-spesa-'.$organization->j_seo;
  // $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/fai-la-spesa-'.$organization->j_seo;
  $menus[$i]['url'] = '/fai-la-spesa';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Stampe';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/stampe-'.$organization->j_seo;
  $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/stampe-'.$organization->j_seo;
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Produttori';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/gmaps-produttori';
  $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/gmaps-produttori';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Gasisti';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/gmaps';
  $menus[$i]['url'] = 'admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/gmaps';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Carrello';
  $menus[$i]['url'] = '/user-cart';
  $menus[$i]['target'] = '';
} // end if(!empty($organization))
// debug($menus);
?> 
<nav class="navbar navbar-expand-lg static-top">

    <a class="navbar-brand" href="#">
      <img src="/img/loghi/150h50.png" alt="">
      <h1>Gestionale web per Gruppi d'acquisto solidale e D.E.S.</h1>
    </a>

  <!-- a class="navbar-brand" href="#"><div class="logo"></div></a -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

<?php
echo '<div class="collapse navbar-collapse" id="navbarNav">';
echo '<ul class="navbar-nav ml-auto">';

foreach($menus as $numResults => $menu) {
  echo '<li class="nav-item ';
  if($numResults==0) echo ' active';
  echo '">';
  echo '<a class="nav-link" target="'.$menu['target'].'" href="'.$menu['url'].'">'.$menu['label'].'</a>';
  echo '</li>';
}

echo '<li class="nav-item">';
echo '<a href="" class="nav-link" data-toggle="modal" data-target="#cashesUserModal">'.$user->get('username').'</a>';
echo '</li>';
echo '</ul>';
echo '</div>';
echo '</nav>';

