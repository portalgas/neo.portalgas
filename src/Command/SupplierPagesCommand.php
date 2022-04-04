<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Filesystem\File;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\SitemapComponent;
use Cake\ORM\TableRegistry;

/*
 * creo sitemap.xml
 * /var/www/neo.portalgas/src/Command/Sh/supplierPages.sh
 * /var/www/neo.portalgas/bin/cake SupplierPages
 */ 
class SupplierPagesCommand extends Command
{
    private $_portalgas_app_root = '';
    private $_portalgas_fe_url = '';
    private $_neo_portalgas_fe_url = '';

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $debug = false;

        $config = Configure::read('Config');
       // $this->_portalgas_app_root = '/home/luca/progetti/portalgas';
        $this->_portalgas_app_root = $config['Portalgas.App.root'];
        $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
        $this->_neo_portalgas_fe_url = 'https://neo.portalgas.it';
        $file_path = $this->_portalgas_app_root . DS . 'produttori';

        /*
         * cancello file di log
         */
        if(file_exists(LOGS.'shell.log'))
            unlink(LOGS.'shell.log');

        if($debug) {
            $this->io = $io;
            $this->io->out('SupplierPages start');
        }

        $suppliersTable = TableRegistry::get('Suppliers');

        /*
         * PRODUTTORI
        */
        $suppliers = $this->_getSuppliers($debug);

        /*
         * INDEX
        */
        $html = '';

        $options = [];
        $options['title'] = "Elenco produttori che aderiscono al gestionale web e app per i gruppi d'acquisto solidale - GAS - e i DES - PortAlGas";
        $html .= $this->_getHtmlHeader($options);

        $html .= '<h1>Elenco produttori che adeiscono al progetto PortAlGas <small>(gestionale per Gruppi d\'acquisto solidale)</small></h1>';
        $html .= '<ul>';
        foreach($suppliers as $supplier) {
            $html .= '<li>';
            $html .= '<a title="'.$supplier->name.'" href="'.$this->_portalgas_fe_url .'/produttori/'.$supplier->slug.'.html">'.$supplier->name;
            if (!empty($supplier->descrizione))
                $html .= ' - '.$supplier->descrizione;
            $html .= '</a>';
        }
        $html .= '</ul>';
        $html .= $this->_getHtmlFooter();

        $file_path_full = $file_path . DS . 'index.html';
        if($debug) debug('file '.$file_path_full);
        $file = new File($file_path_full, true, 0644);
        // if($debug) debug($file);
        // if($debug) debug($html);
        try {
            $file->write($html);
        }
        catch (Exception $e) {
            debug($e->getMessage());
        }
        $file->close();

        /*
         * PAGES
        */
        foreach($suppliers as $supplier) {
            // dd($supplier);
            $html = '';

            $options = [];
            $options['title'] = "Produttore ".$supplier->name." che rifornisce i G.A.S. (Gruppi d'Acquisto Solidale)";
            $html .= $this->_getHtmlHeader($options);

            $html .= '<h1>' . $supplier->name . '</h1>';
            $html .= '<h3>Vai alla pagina del produttore <a title="'.$supplier->slug.'" href="'.$this->_neo_portalgas_fe_url.'/site/produttore/'.$supplier->slug.'">'.$supplier->name.'</a></h3>';

            if (!empty($supplier->img1)) {
                $img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $supplier->img1);
                $img_path_supplier = $this->_portalgas_app_root . $img_path_supplier;

                $url = '';
                if(file_exists($img_path_supplier)) {
                    $url = sprintf($this->_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $supplier->img1);
                }
                $html .= '<p>';
                $html .= '<img style="max-width:250px" src="'.$url.'" title="'.$supplier->name.' alt="'.$supplier->name.'">';
                $html .= '</p>';
            }

            $html .= '<p><b>Categoria</b> ';
            $html .= $supplier->categories_supplier->name;
            $html .= '</p>';

            if (!empty($supplier->descrizione)) {
                $html .= '<p>';
                $html .= $supplier->descrizione;
                $html .= '</p>';
            }

            $html .= '<h2>'.__('Contatti').'</h2>';
            $html .= '<p>';
            $html .= '<b>'.__('Address').'</b> ';
            $html .= $supplier->indirizzo.' '.$supplier->localita.' '. $supplier->cap.' '.$supplier->provincia;
            $html .= '</p>';
            if (!empty($supplier->telefono))
                $html .= '<p><b>Telefono</b> '.$supplier->telefono.'</p>';
            if(!empty($supplier->mail))
                $html .= '<p></p><a href="mailto:'.$supplier->mail.'">'. $supplier->mail.'</a></p>';
            if(!empty($supplier->www))
                $html .= '<p>Sito intenet <a target="'. $supplier->www.'" href="'.$supplier->www.'">'. $supplier->www.'</a></p>';
            if (!empty($supplier->cf))
                $html .= '<p><b>Codice fiscale</b> ';$supplier->cf.'</p>';
            if (!empty($supplier->cf))
                $html .= '<p><b>Partita iva</b> ';$supplier->piva.'</p>';

            if(!empty($supplier->content->introtext)) {
                $html .= '<h2>'.__('Descri').'</h2>';
                $html .= '<p>';
                if(!empty($supplier->content->introtext))
                    $html .= str_replace('{flike}', '', $supplier->content->introtext);
                if(!empty($supplier->content->fulltext)) {}
                    $html .= str_replace('{flike}', '', $supplier->content->fulltext);
                $html .= '</p>';
            }

            if(!empty($supplier->suppliers_organizations)) {
                $html .= '<h2>G.A.S. (Gruppi d\'acquisto solidale) che collaborano con il produttore</h2>';
                $html .= '<ul style="list-style-type:none;">';
                foreach ($supplier->suppliers_organizations as $suppliers_organization) {

                    $url = sprintf($this->_portalgas_fe_url.Configure::read('Organization.img.path.full'), $suppliers_organization->organization->img1);

                    $html .= '<li>';
                    $html .= '<a href="'.$this->_portalgas_fe_url.'/home-'.$suppliers_organization->organization->j_seo.'" title="'.$suppliers_organization->organization->name.'">';

                    $html .= '<img style="max-width:150px" src="'.$url.'" title="'.$suppliers_organization->organization->name.' alt="'.$suppliers_organization->organization->name.'">';

                    $html .= $suppliers_organization->organization->name.' ';
                    if(!empty($suppliers_organization->organization->localita))
                        $html .= $suppliers_organization->organization->localita;
                    $html .= '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
            $html .= $this->_getHtmlFooter();

            $file_path_full = $file_path . DS . $supplier->slug.'.html';
            if($debug) debug('file '.$file_path_full);
            $file = new File($file_path_full, true, 0644);
            // if($debug) debug($file);
            // if($debug) debug($html);
            try {
                $file->write($html);
            }
            catch (Exception $e) {
                debug($e->getMessage());
            }
            $file->close();

        } // loop

        if($debug) {
            $this->io = $io;
            $this->io->out('SupplierPages end');
        }
    }

    private function _getSuppliers($debug) {

        $suppliersTable = TableRegistry::get('Suppliers');

        $where = ['Suppliers.stato' => 'Y',
                  'Suppliers.slug != ' => ''];
        return $suppliersTable->find()
            ->contain(['CategoriesSuppliers', 'Content',
                      'SuppliersOrganizations' => ['Organizations' => ['conditions' => ['Organizations.stato' => 'Y']]]
            ])
            ->where($where)
            ->order(['Suppliers.name'])
            // ->limit(1)
            ->all();
    }

    private function _getHtmlHeader($options) {

        isset($options['title']) ? $title = $options['title']: $title = "PortAlGas, il gestionale web e app per i gruppi d'acquisto solidale - GAS - e i DES - PortAlGas";

        $html = '		
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it-it" lang="it-it" dir="ltr" >
    <head>
        <base href="'.$this->_portalgas_fe_url.'/" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="gruppi di acquisto solidale, gas, des, gestionale, software, programma, distretto economia solidale, consegne, ordini, referenti, km zero, sostenibilità" />
        <meta name="description" content="Gestionale web per G.A.S. (GAS gruppo d\'acquisto solidale) e D.E.S. (DES distretto economia solidale)" />
        <title>'.$title.'</title>
        <link href="'.$this->_portalgas_fe_url.'/templates/v01/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="'.$this->_portalgas_fe_url.'/templates/v01/css/bootstrap.min.css">
        <link rel="stylesheet" href="'.$this->_portalgas_fe_url.'/templates/v01/css/font-awesome.min.css">
        <link rel="stylesheet" href="'.$this->_portalgas_fe_url.'/templates/v01/css/my-bootstrap-v03.min.css">
    </head>
    <body>
        <div class="container">
            <header>
                <div class="hidden-xs col-md-6 col-sm-6">
                    <a href="'.$this->_portalgas_fe_url.'/"><div class="logo hidden-xs"></div>
                        <h1 style="position: absolute; font-size: 14px; color: rgb(10, 101, 158); opacity: 0.7; top: 45px;">Gestionale web per Gruppi d\'acquisto solidale e D.E.S.</h1>
                    </a>
                </div>
                <div class="col-xs-12 col-md-6 col-sm-6"></div>
            </header>

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
                        <li class="item-101 current active"><a href="'.$this->_portalgas_fe_url.'/" >Home</a></li>
                        <li class="item-107"><a href="'.$this->_portalgas_fe_url.'/cos-e-un-g-a-s-gruppo-d-acquisto-solidale" >Cos\'è un G.A.S.</a></li>
                        <li class="item-110"><a href="'.$this->_portalgas_fe_url.'/gmaps-gas" >I G.A.S.</a></li>
                        <li class="item-112"><a href="'.$this->_portalgas_fe_url.'/contattaci" >Scrivici</a></li>
                        <li class="item-150"><a href="https://neo.portalgas.it/site/produttori" >Produttori</a></li>
                        <li class="item-111"><a class=" hide" href="'.$this->_portalgas_fe_url.'/gestionale-web-gas" >Presentazione</a></li>
                     </ul>	   
                </div>
            </nav>

	        <div class="row">
		        <div class="col-xs-12 col-md-12">';

            return $html;
    }

    private function _getHtmlFooter() {

        $html = '</div> <!-- col-xs-12 col-md-12 -->
            </div> <!-- row -->
        </div> <!-- container -->

<footer>
	<div class="footer-above">
		<div class="container">
			<div class="row">
				<div class="footer-col col-md-3 col-xs-12 col-sm-6 text-left">

					<ul class="social">
						<li>
							<a target="_blank" href="https://facebook.com/portalgas.it"><img border="0" src="'.$this->_portalgas_fe_url.'/images/cake/ico-social-fb.png" alt="PortAlGas su facebook" title="PortAlGas su facebook"> Facebook</a>
						</li>
						<li>
							<a target="_blank" href="https://www.youtube.com/channel/UCo1XZkyDWhTW5Aaoo672HBA"><img border="0" src="'.$this->_portalgas_fe_url.'/images/cake/ico-social-youtube.png" alt="PortAlGas su YouTube" title="PortAlGas su YouTube"> YouTube</a>
						</li>
						<!-- li>
							<a target="_blank" href="/mobile"><img border="0" src="/images/cake/ico-mobile.png" alt="PortAlGas per tablet e mobile" title="PortAlGas per tablet e mobile"> Mobile</a>
						</li -->					
					</ul>			
				
				</div>
				<div class="footer-col col-md-3 col-xs-12 col-sm-6">
					<ul class="social">
						<li>
							<a href="https://manuali.portalgas.it" target="_blank"><img border="0" title="I manuali di PortAlGas" alt="I manuali di PortAlGas" src="'.$this->_portalgas_fe_url.'/images/cake/ico-manual.png"> Manuali</a>
						</li>						
						<li>
							<a target="_blank" href="https://github.com/portalgas/site"><img border="0" src="'.$this->_portalgas_fe_url.'/images/cake/ico-github.png" alt="il codice di PortAlGas disponibile per chi desidera partecipare" title="il codice di PortAlGas disponibile per chi desidera partecipare"> GitHub</a>
						</li>
					</ul>	
				</div>				
				<div class="footer-col col-md-3 col-xs-12 col-sm-6"></div>
				<div class="footer-col col-md-3 col-xs-12 col-sm-6 text-right">
					<ul class="social">
						<li>
							<a href="'.$this->_portalgas_fe_url.'/12-portalgas/2-termini-di-utilizzo" title="Leggi le condizioni di utilizzo di PortAlGas">Termini di utilizzo</a>
						</li>
						<li>
							<a href="'.$this->_portalgas_fe_url.'/12-portalgas/143-come-sono-utilizzati-i-cookies-da-parte-di-portalgas" title="Leggi come sono utilizzati i cookies da parte di PortAlGas">Utilizzo dei cookies</a>
						</li>
						<li>
							<a href="'.$this->_portalgas_fe_url.'/12-portalgas/103-bilancio" title="Leggi il bilancio di PortAlGas">Bilancio</a>
						</li>	
					</ul>
				</div>
			</div> <!-- row -->
		</div> <!-- container -->
	</div> <!-- footer-above -->


	<div class="footer-below">
        <div class="container">
                <div class="row">
                    <div class="col-lg-2 text-center">
	                	<a href="https://www.iubenda.com/privacy-policy/56886383" class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe " title="Privacy Policy ">Privacy Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
	                </div>
                    <div class="col-lg-8 text-center">
						Copyright &copy; '.date('Y').' PortAlGas. All Rights Reserved.
					</div>
                    <div class="col-lg-2 text-center">	
						<a href="mailto:info@portalgas.it" title="Scrivi una mail a info@portalgas.it">info@portalgas.it</a>
                    </div>
                </div>
        </div>
	</div>
</footer>
</body>
</html>	';
        return $html;
    }
}