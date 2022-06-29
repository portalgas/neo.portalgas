<?php 
/*
 * link menu' su portalas rimappati in /templates/v01/index.php
 */
use Cake\Core\Configure; 

$user = $this->Identity->get();
$i=0;

if(!isset($user) || empty($user)) {
  $i++;
  $menus[$i]['label'] = __('Scrivici');
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/contattaci';
  $menus[$i]['target'] = ''; 
  $i++;
  $menus[$i]['label'] = __('Notizie');
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/notizie';
  $menus[$i]['target'] = ''; 
  $i++;
  $menus[$i]['label'] = __('Login');
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/login';
  $menus[$i]['target'] = ''; 
}

if(!empty($organization)) {
  $i++;
  $menus[$i]['label'] = 'Acquista SocialMarket';
  $menus[$i]['url'] = '/social-market';
  $menus[$i]['target'] = '';
}

if(!empty($organization)) {
  $i++;
  $menus[$i]['label'] = 'Produttori';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/gmaps-produttori';
  $menus[$i]['url'] = '/admin/joomla25Salts?scope=FE&c_to=/home-'.$organization->j_seo.'/gmaps-produttori';
  // $menus[$i]['url'] = '/site/gas-produttori';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Carrello';
  $menus[$i]['url'] = '/user-cart';
  $menus[$i]['target'] = '';
} // end if(!empty($organization))
// debug($menus);

echo '<nav class="navbar navbar-expand-lg static-top">';
echo '    <a class="navbar-brand" href="/">';
echo '      <img src="/img/loghi/150h50.png" alt="">';
echo '      <h1>Gestionale web per Gruppi d\'acquisto solidale e D.E.S.</h1>';
echo '    </a>';

echo '  <!-- a class="navbar-brand" href="#"><div class="logo"></div></a -->';
echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">';
echo '  <span class="navbar-toggler-icon"></span>';
echo '</button>';

echo '<div class="collapse navbar-collapse" id="navbarNav">';
echo '<ul class="navbar-nav ml-auto">';

foreach($menus as $numResults => $menu) {
  echo '<li class="nav-item ';
  // if($numResults==0) echo ' active'; lo fa js Layout/vue.ctp
  echo '">';
  echo '<a class="nav-link" target="'.$menu['target'].'" href="'.$menu['url'].'">'.$menu['label'].'</a>';
  echo '</li>';
}

if(isset($user) && !empty($user)) {
  echo '<li class="nav-item">';
  echo '<a href="" class="nav-link" data-toggle="modal" data-target="#cashesUserModal">'.$user->get('username').'</a>';
  echo '</li>';  
}

echo '</ul>';
echo '</div>';
echo '</nav>';