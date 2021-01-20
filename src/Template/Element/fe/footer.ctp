<?php 
use Cake\Core\Configure; 
?>

<style>
footer {
    margin-top: 15px;
    color: #fff;
    background-color: #2c3e50;
    padding: 20px 20px 10px 20px;
}
footer .box {
    margin-bottom: 5px;
}
footer a {
    color: #fff;
    text-decoration: none;
}
footer a:hover {
    color: #fa824f !important; /* orange */
    text-decoration: none;
}
footer ul.social {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
footer ul li:not(:last-child) {
    margin-right: 0.5rem;
}
footer ul li {
    display: inline-block;
}
footer ul.social li a {
    font-size: 22px;
    line-height: 50px;
    display: block;
    width: 48px;
    height: 48px;
    text-align: center;
    transition: all 0.3s;
    color: white;
    border-radius: 100%;
    outline: none;
    background-color: #fa824f; /* orange */
}
footer ul.social li a:hover {
  color: #2c3e50 !important;
}
</style>
<footer>
  <div class="row">
    <div class="box col-md-4 col-xs-12 col-sm-6 text-left d-none d-md-block d-lg-block d-xl-block">
        Copyright &copy; <?php echo date('Y');?> PortAlGas. All Rights Reserved.            
    </div>

    <div class="box col-md-4 col-xs-12 col-sm-6 text-center">
      <ul class="social">
        <li>
          <a href="mailto:info@portalgas.it"" title="Scrivici una mail a info@portalgas.it">
            <i class="fas fa-envelope-square"></i></a>
        </li>  
        <li>
          <a href="https://manuali.portalgas.it" target="_blank" title="I manuali di PortAlGas">
            <i class="fas fa-book"></i></a>
        </li>          
        <li>
          <a target="_blank" href="https://facebook.com/portalgas.it" title="PortAlGas su facebook">
            <i class="fab fa-facebook-square"></i></a>
        </li>
        <li>
          <a target="_blank" href="https://www.youtube.com/channel/UCo1XZkyDWhTW5Aaoo672HBA" title="PortAlGas su YouTube">
            <i class="fab fa-youtube-square"></i></a>
        </li>       
        <li>
          <a target="_blank" href="https://github.com/portalgas/site" title="il codice di PortAlGas disponibile per chi desidera partecipare">
            <i class="fab fa-github-square"></i></a>
        </li>
      </ul>
    </div>
      
    <div class="box col-md-4 col-xs-12 col-sm-6 text-right d-none d-md-block d-lg-block d-xl-block">
      <ul class="link">
        <li>
          <a href="<?php echo $config['Portalgas.fe.url'];?>/12-portalgas/2-termini-di-utilizzo" title="Leggi le condizioni di utilizzo di PortAlGas">Termini di utilizzo</a>
        </li>
        <li>
          <a href="<?php echo $config['Portalgas.fe.url'];?>/12-portalgas/143-come-sono-utilizzati-i-cookies-da-parte-di-portalgas" title="Leggi come sono utilizzati i cookies da parte di PortAlGas">Utilizzo dei cookies</a>
        </li>
        <li>
          <a href="<?php echo $config['Portalgas.fe.url'];?>/12-portalgas/103-bilancio" title="Leggi il bilancio di PortAlGas">Bilancio</a>
        </li> 
      </ul>
    </div>
  </div> <!-- row -->
</footer>