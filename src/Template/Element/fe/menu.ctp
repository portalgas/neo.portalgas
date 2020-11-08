 <?php 
use Cake\Core\Configure; 

$i=0;
$menus = [];
$menus[$i]['label'] = 'Home';
$menus[$i]['url'] = $config['Portalgas.fe.url'];
$menus[$i]['target'] = '';

if(!empty($organization)) {
  $i++;
  $menus = [];
  $menus[$i]['label'] = 'Consegne';
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/consegne-'.$organization->j_seo;
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Acquista';
  // $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/fai-la-spesa-'.$organization->j_seo;
  $menus[$i]['url'] = '/';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Stampe';
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/stampe-'.$organization->j_seo;
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Produttori';
  $menus[$i]['url'] = $config['Portalgas.fe.url'].'/home-'.$organization->j_seo.'/gmaps-produttori';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = 'Carrello';
  $menus[$i]['url'] = '/user-cart';
  $menus[$i]['target'] = '';
  $i++;
  $menus[$i]['label'] = $user->get('username');
  $menus[$i]['url'] = '';
  $menus[$i]['target'] = '';
} // end if(!empty($organization))
// debug($menus);
?> 
<nav class="navbar navbar-expand-lg static-top">

    <a class="navbar-brand" href="#">
      <img src="https://www.portalgas.it/images/cake/loghi/0/150h50.png" alt="">
      <h1>Gestionale web per Gruppi d'acquisto solidale e D.E.S.</h1>
    </a>

  <!-- a class="navbar-brand" href="#"><div class="logo"></div></a -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <?php
      foreach($menus as $numResults => $menu) {
        echo '<li class="nav-item ';
        if($numResults==0) echo ' active';
        echo '">';
        echo '<a class="nav-link" target="'.$menu['target'].'" href="'.$menu['url'].'">'.$menu['label'].'</a>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>
</nav>

<?php
/*
<nav role="navigation" class="navbar navbar-default">
 
      <div class="navbar-header">
          <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a href="#" class="navbar-brand visible-xs">PortAlGas</a>
      </div>
      <div id="navbarCollapse" class="collapse navbar-collapse">      
      
    <ul class="menu nav navbar-nav">
      <li class="item-513">
        <a href="<?php echo $config['Portalgas.fe.url'];?>/home-<?php echo $organization->j_seo;?>/consegne-<?php echo $organization->j_seo;?>" >Consegne</a>
      </li>
      <li class="item-515 current active">
        <a href="<?php echo $config['Portalgas.fe.url'];?>/home-<?php echo $organization->j_seo;?>/fai-la-spesa-<?php echo $organization->j_seo;?>" >Acquista</a>
      </li>
      <li class="item-518">
        <a href="<?php echo $config['Portalgas.fe.url'];?>/home-<?php echo $organization->j_seo;?>/stampe-<?php echo $organization->j_seo;?>" >Stampe</a>
      </li>
      <li class="item-521">
        <a href="/home-<?php echo $organization->j_seo;?>/gmaps" >Gasisti</a></li><li class="item-522"><a href="<?php echo $config['Portalgas.fe.url'];?>/home-<?php echo $organization->j_seo;?>/gmaps-produttori" >Produttori</a>
      </li>
    </ul>

        <div class="nav navbar-nav navbar-right"></div>
      </div>
  
  </nav>
*/
  ?>