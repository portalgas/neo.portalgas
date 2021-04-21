<?php 
/*
 * link menu' su portalas rimappati in /templates/v01/index.php
 */
use Cake\Core\Configure; 

$user = $this->Identity->get();
$socialMarketIsActive = $config['SocialMarket.isActive'];

$i=0;
$menus = [];
$menus[$i]['label'] = 'Home';
$menus[$i]['url'] = $config['Portalgas.fe.url'];
$menus[$i]['target'] = '';

$menus = [];

if($socialMarketIsActive) {
  $i++;
  $menus[$i]['label'] = __('SocialMarket');
  $menus[$i]['url'] = '/site/social-market';
  $menus[$i]['target'] = '';   
}
// debug($menus);
?> 
<nav class="navbar navbar-expand-lg static-top">

    <a class="navbar-brand" href="#">
      <img src="/img/loghi/150h50.png" alt="">
      <h1>Social Market</h1>
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

echo '</ul>';
echo '</div>';
echo '</nav>';