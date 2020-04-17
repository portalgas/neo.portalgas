<?php 
use Cake\Core\Configure; 

$config = Configure::read('Config');
$portalgas_bo_url_login = $config['Portalgas.bo.url.login'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo Configure::read('Theme.title'); ?> | <?php echo $this->fetch('title'); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  


  <!-- Bootstrap 3.3.7 -->
  <?php // echo $this->Html->css('AdminLTE./bower_components/bootstrap/dist/css/bootstrap.min'); ?>
  <link href="/css/fe/bootstrap.min.css" rel="stylesheet" type="text/css">

  <!-- Custom fonts for this template -->
  <link href="/css/fe/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Plugin CSS -->
  <link href="/css/fe/magnific-popup.css" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template -->
  <link href="/css/fe/freelancer.min.css" rel="stylesheet">
	
	<style>
	.masthead .masthead-avatar {
		width: 15rem;
	}
	</style>
</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg bg-secondary fixed-top text-uppercase" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="#page-top"><?php echo Configure::read('Theme.title');?></a>
      <button class="navbar-toggler navbar-toggler-right text-uppercase bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="<?php echo $portalgas_bo_url_login;?>">Backoffice</a>
          </li>
          <!-- li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#portfolio">Portfolio</a>
          </li -->
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#about">Il progetto</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#contact">Contattaci</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="masthead bg-primary text-white text-center">
    <div class="container">
	  <img class="masthead-avatar mb-5" src="img/avataaars.svg" alt="">
      <!-- img class="img-fluid mb-5 d-block mx-auto" src="img/profile.png" alt="" -->
      <h1 class="text-uppercase mb-0"><?php echo Configure::read('Theme.title');?></h1>
      <hr class="star-light">
      <h2 class="font-weight-light mb-0">Torino <?php echo date('Y');?></h2>
    </div>
  </header>

  <!-- Portfolio Grid Section 
  <section class="portfolio" id="portfolio">
    <div class="container">
      <h2 class="text-center text-uppercase text-secondary mb-0">Portfolio</h2>
      <hr class="star-dark mb-5">
      <div class="row">
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-1">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/cabin.png" alt="">
          </a>
        </div>
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-2">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/cake.png" alt="">
          </a>
        </div>
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-3">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/circus.png" alt="">
          </a>
        </div>
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-4">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/game.png" alt="">
          </a>
        </div>
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-5">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/safe.png" alt="">
          </a>
        </div>
        <div class="col-md-6 col-lg-4">
          <a class="portfolio-item d-block mx-auto" href="#portfolio-modal-6">
            <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
              <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                <i class="fas fa-search-plus fa-3x"></i>
              </div>
            </div>
            <img class="img-fluid" src="img/portfolio/submarine.png" alt="">
          </a>
        </div>
      </div>
    </div>
  </section>
  -->

  <!-- About Section -->
  <section class="bg-primary text-white mb-0" id="about">
    <div class="container">
      <h2 class="text-center text-uppercase text-white">Il progetto</h2>
      <hr class="star-light mb-5">
      <div class="row">
        <div class="col-lg-4 ml-auto">
			<p>La nostra idea è quella di creare un gestionale web ed un app mobile (denominato PortAlGas) che permetta ai G.A.S. (GAS gruppo d'acquisto solidale) e ai D.E.S. (distretto di economia solidale) una gestione completa e tecnologicamente avanzata dei propri ordini, governandone tutto il ciclo di vita.</p>
			<p>Il cuore del progetto è l'idea di partire da uno strumento di e-commerce (semplice ed intuitivo) integrando le funzionalità tipiche degli acquisti dei GAS.</p>
			<p>Una gestione a schede guidate dal luogo e dalla data di consegna, integrando ordini ed articoli validi espressamente per la consegna in oggetto il tutto rendendo queste attività il più semplici possibili e automatizzate.</p>
			<p>L'attore principale è il referente di prodotto, coadiuvato dal responsabile del GAS e da altre figure di aiuto (Tesoriere, Co-referente...)</p>
			<p>Anche i produttori hanno un ruolo fondamentale per i Gas, avere la possibilità di poter consultare un archivio dei Produttori comune a tutti i GAS aderenti è a nostro avviso un vantaggio.</p>
			<p>Solo i produttori selezionati da un Gas aderente possono entrare in questo archivio comune, in questo modo risulta estremamente facile la ricerca di nuovi produttori.</p>        
        </div>
        <div class="col-lg-4 mr-auto">
			<p>Di seguito elenchiamo le funzionalità principali del gestionale web PortAlGas:</p>
			<ul>
			<li>Gestione a Moduli (si attivano solo le funzionalità desiderate ed utili)</li>
			<li>Soci (anagrafiche membri G.A.S.)</li>
			<li>Classificazione Utenti (Livelli diversi di abilitazione)</li>
			<li>Fornitori (anagrafiche produttori Gas) e Archivio Fornitori generici in comune agli altri GAS</li>
			<li>Articoli (Elenco Prodotti dei Fornitori)</li>
			<li>Date e luoghi di Ritiro Prodotti</li>
			<li>Avanzamento Stati Ordini
					<ul>
					<li>Ordini (Inserimento, Acquisto tramite funzionalità di e-commerce per G.A.S)</li>
					<li>Avanzamento Ordini (avvisi automatici ai referenti, gestione capienza x collo; Bancale)</li>
					<li>Chiusure Ordini automatiche a scadenza periodo o al raggiungimento del limite di capienza.</li>
					<li>Invio Ordini (Variazioni ordini per colli, prezzi, etc stampe per Invio Ordini ai Fornitori)</li>
					<li>Consegne (controllo della merce ricevuta ed eventuali variazioni post-consegna)</li>
					<li>Diversi Algoritmi di calcolo delle Spese di Trasporto (per Utente, a peso, a collo...)</li>
					<li>Post-Consegna (Gestioni ordini a peso; a busta per utente; a collo.. )</li>
					<li>Tesoreria (Riepilogo Conti e gestione Pagamenti)</li>
					</ul>
					</li>
			<li>Gestione Dispensa</li>
			<li>Back-Office di Gestione</li>
			</ul>
        </div>        
      </div>  
      <div class="row">
        <div class="col-lg-10 ml-auto">
			<p>Dopo alcuni anni di gestione di un Gas ci siamo resi conto che le possibilità dei sw attuali non rispecchiavano le nostre esigenze abbiamo quindi iniziato ad ampliare le funzionalità del nostro Sito con alcune funzionalità.</p>
			<p>Queste richiedono però un intervento quotidiano manuale e non è proprio il massimo quindi abbiamo scelto di pensarne uno tutto nuovo.</p> 
        </div>        
      </div>  		
      <div class="text-center mt-4">
        <a class="py-3 px-0 px-lg-3 rounded js-scroll-trigger btn btn-xl btn-outline-light" href="#contact">
          <i class="fas fa-download mr-2"></i>
          Contattaci
        </a>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact">
    <div class="container">
      <h2 class="text-center text-uppercase text-secondary mb-0">Contattaci</h2>
      <hr class="star-dark mb-5">
      <div class="row">
        <div class="col-lg-8 mx-auto">

          <form name="sentMessage" id="contactForm" novalidate="novalidate" action="mailto:info@portalgas.it" method="post" enctype="text/plain">
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Nome</label>
                <input class="form-control" id="name" type="text" placeholder="Nome" required="required" data-validation-required-message="Inserisci il tuo nome.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Email</label>
                <input class="form-control" id="email" type="email" placeholder="Email" required="required" data-validation-required-message="Inserisci la tua mail.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Telefono</label>
                <input class="form-control" id="phone" type="tel" placeholder="Telefono" required="required" data-validation-required-message="Inserisci il tuo numero di telefono.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label>Messaggio</label>
                <textarea class="form-control" id="message" rows="5" placeholder="Testo" required="required" data-validation-required-message="Inserisci un testo."></textarea>
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <br>
            <div id="success"></div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-xl" id="sendMessageButton">Invia</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer text-center">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-5 mb-lg-0">
          <h4 class="text-uppercase mb-4">Torino</h4>
          <p class="lead mb-0">
        </div>
        <div class="col-md-4 mb-5 mb-lg-0">
          <h4 class="text-uppercase mb-4">Web</h4>


        <!-- p>
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="http://www.portalgas.it/mobile">
                <i class="fab fa-fw fa-mobile"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="http://manuali.portalgas.it">
                <i class="fab fa-fw fa-book"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://www.youtube.com/channel/UCo1XZkyDWhTW5Aaoo672HBA">
                <i class="fab fa-fw fa-youtube"></i>
              </a>
            </li>
         </ul>
		</p -->
		
		<p>
          <ul class="list-inline mb-0">
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://www.youtube.com/channel/UCo1XZkyDWhTW5Aaoo672HBA">
                <i class="fab fa-fw fa-youtube"></i>
              </a>
            </li>		  
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://facebook.com/portalgas.it">
                <i class="fab fa-fw fa-facebook-f"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://itunes.apple.com/us/app/portalgas/id1133263691">
                <i class="fab fa-fw fa-apple"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a class="btn btn-outline-light btn-social text-center rounded-circle" href="https://play.google.com/store/apps/details?id=com.ionicframework.portalgas">
                <i class="fab fa-fw fa-android"></i>
              </a>
            </li>
          </ul>
		</p>
		
        </div>
        <div class="col-md-4">
          <h4 class="text-uppercase mb-4">Contatti</h4>
          <p class="lead mb-0">
            <a href="info@portalgas.it" target="_blank">info@portalgas.it</a>
          </p>
        </div>
      </div>
    </div>
  </footer>

  <div class="copyright py-4 text-center text-white">
    <div class="container">
      <small>Copyright &copy; Your Website 2019</small>
    </div>
  </div>

  <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
  <div class="scroll-to-top d-lg-none position-fixed ">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top">
      <i class="fa fa-chevron-up"></i>
    </a>
  </div>

  <!-- Portfolio Modals -->

  <!-- Portfolio Modal 1 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-1">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/cabin.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Portfolio Modal 2 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-2">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/cake.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Portfolio Modal 3 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-3">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/circus.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Portfolio Modal 4 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-4">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/game.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Portfolio Modal 5 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-5">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/safe.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Portfolio Modal 6 -->
  <div class="portfolio-modal mfp-hide" id="portfolio-modal-6">
    <div class="portfolio-modal-dialog bg-white">
      <a class="close-button d-none d-md-block portfolio-modal-dismiss" href="#">
        <i class="fa fa-3x fa-times"></i>
      </a>
      <div class="container text-center">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h2 class="text-secondary text-uppercase mb-0">Project Name</h2>
            <hr class="star-dark mb-5">
            <img class="img-fluid mb-5" src="img/portfolio/submarine.png" alt="">
            <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia neque assumenda ipsam nihil, molestias magnam, recusandae quos quis inventore quisquam velit asperiores, vitae? Reprehenderit soluta, eos quod consequuntur itaque. Nam.</p>
            <a class="btn btn-primary btn-lg rounded-pill portfolio-modal-dismiss" href="#">
              <i class="fa fa-close"></i>
              Close Project</a>
          </div>
        </div>
      </div>
    </div>
  </div>


<!-- jQuery 3 -->
<?php // echo $this->Html->script('AdminLTE./bower_components/jquery/dist/jquery.min'); ?>
<!-- Bootstrap 3.3.7 -->
<?php // echo $this->Html->script('AdminLTE./bower_components/bootstrap/dist/js/bootstrap.min'); ?>

  <!-- Bootstrap core JavaScript -->
  <script src="/js/fe/jquery.min.js"></script>
  <script src="/js/fe/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="/js/fe/jquery.easing.min.js"></script>
  <script src="/js/fe/jquery.magnific-popup.min.js"></script>

  <!-- Contact Form JavaScript -->
  <script src="js/fe/jqBootstrapValidation.js"></script>
  <script src="js/fe/contact_me.js"></script>

  <!-- Custom scripts for this template -->
  <script src="js/fe/freelancer.min.js"></script>


</body>
</html>
