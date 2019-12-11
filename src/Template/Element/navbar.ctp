<?php
Use Cake\Core\Configure;
?>
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/"><img src="/img/logo-menu.jpg"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="/">Home <span class="sr-only">(current)</span></a></li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-list"></span> Anagrafica <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li>
                <a target="_self" href="/geo-regions">Elenco Regioni</a>
            </li>
            <li>
				<a target="_self" href="/geo-regions/add">Nuova Regione</a>
			</li>
            <li>
                <a target="_self" href="/geo-provinces">Elenco Province</a>
            </li>
            <li>
				<a target="_self" href="/geo-provinces/add">Nuova Provincia</a>
			</li>
            <li>
                <a target="_self" href="/geo-comunes">Elenco Comuni</a>
            </li>
            <li>
				<a target="_self" href="/geo-comunes/add">Nuovo Comune</a>
			</li>
			<li role="separator" class="divider"></li>
            <li>
                <a target="_self" href="/collaborator-types">Elenco Tipologia collaboratori</a>
            </li>
            <li>
                <a target="_self" href="/collaborator-types/add">Nuova Tipologia collaboratore</a>
            </li>
            <li>
                <a target="_self" href="/collaborator-activities">Elenco attività collaboratori</a>
            </li>
            <li>
                <a target="_self" href="/collaborator-activities/add">Nuova attività collaboratori</a>
            </li>
			<li role="separator" class="divider"></li>
            <li>
                <a target="_self" href="/price-types">Elenco Tipologia prezzi</a>
            </li>
            <li>
                <a target="_self" href="/price-types/add">Nuova Tipologia prezzi</a>
            </li>
            <li>
                <a target="_self" href="/offer-detail-states">Elenco stati offerta dettaglio</a>
            </li>
            <li>
                <a target="_self" href="/offer-types">Elenco Tipologia servizi</a>
            </li>
            <li>
                <a target="_self" href="/offer-types/add">Nuova Tipologia servizi</a>
            </li>			
            <li>
                <a target="_self" href="/pay-types">Elenco Tipologia pagamenti</a>
            </li>
            <li>
                <a target="_self" href="/pay-types/add">Nuova Tipologia pagamenti</a>
            </li>
         </ul>
        </li>


        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Utenti <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li>
                <a target="_self" href="/users">Elenco Account</a>
            </li>
            <li>
				<a target="_self" href="/users/add">Nuovo Account</a>
			</li>
            <li>
                <a target="_self" href="/roles">Elenco Ruoli</a>
            </li>
            <li>
				<a target="_self" href="/roles/add">Nuovo Ruolo</a>
			</li>
            <li>
                <a target="_self" href="/customers">Elenco Clienti</a>
            </li>
            <li>
				<a target="_self" href="/customers/add">Nuovo cliente</a>
			</li>
            <li>
                <a target="_self" href="/collaborators">Elenco Collaboratori</a>
            </li>
            <li>
				<a target="_self" href="/collaborators/add">Nuovo Collaboratore</a>
			</li>  
			<li role="separator" class="divider"></li>			
            <li>
                <a target="_self" href="/collaborator-collaborator-types">Elenco Tipologie collaboratori + collaboratori</a>
            </li>            
            <li>
                <a target="_self" href="/collaborator-collaborator-types/add">Nuova Tipologia collaboratori + collaboratori</a>
            </li>  			
         </ul>
        </li>
		
		
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-copy"></span> Offerte <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li>
                <a target="_self" href="/offers">Elenco Offerte</a>
            </li>
            <li>
				<a target="_self" href="/offers/add">Nuova offerta</a>
			</li> 			
         </ul>
        </li>		

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-paste"></span> Commesse <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li>
                <a target="_self" href="/quotes">Elenco Commesse</a>
            </li>
            <li>
                <a target="_self" href="/quote-details">Elenco dettaglio commessa</a>
            </li>
            <li>
                <a target="_self" href="/quote-detail-calendars">Elenco dettaglio commesse / calendario</a>
            </li>		
         </ul>
        </li>
		

        <li>
            <li>
                <a target="_self" href="/v-collaborator-payments"><span class="glyphicon glyphicon-list-alt"></span> Vista di sintesi</a>
            </li>           
        </li>
            
      </ul>
	  
		  
      <ul class="nav navbar-nav navbar-right"> 
		<?php
		if($isImpersonated)
			echo '<li>'.$this->Html->link('<button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span> '.__('impersonateLogout').' '.$this->Identity->get('username').'</button>', ['controller' => 'auths', 'action' => 'impersonateLogout'],['escape' => false]).'</li> ';
		?>	  
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/users/view/<?php echo $this->Identity->get('id');?>">Profilo <?php echo $this->Identity->get('username');?></a></li>
            <li><a href="/auths/logout"><button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span> Logout</button></a></li>
          </ul>
        </li>
      </ul>
	  
	  <form class="navbar-form navbar-right">
		<div class="form-group">
		  <input type="text" class="form-control" placeholder="Ricerca offerta/preventivo">
		</div>
		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
	  </form>

	  
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>