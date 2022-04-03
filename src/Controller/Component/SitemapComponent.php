<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Utility\Xml;
use Cake\Filesystem\File;

class SitemapComponent extends Component {

	private $_portalgas_fe_url = '';
    private $_neo_portalgas_fe_url;
    private $_file_path;
    private $_file_name;

    public function __construct(ComponentRegistry $registry, array $config = []) {

        $config = Configure::read('Config');
        $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
        $this->_neo_portalgas_fe_url = 'https://neo.portalgas.it';
        $this->_file_path = $config['Portalgas.App.root'];
        $this->_file_name = 'sitemap.xml';
	}

	public function create($debug=false) {

        $sitemap = '';

        $baseUrl = $this->_portalgas_fe_url;
        $this->autoRender = false;

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
                             xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9 https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
                             xmlns:image="http://www.sitemaps.org/schemas/sitemap-image/1.1" >' . "\n";

        /*
         * pagine statiche
        */
        $urls = $this->_getUrls();
        foreach($urls as $url) {
            // dd($url);
            $sitemap .= $this->_getTagUrl($url);
        }

        /*
         * GAS
        */
        $organizationGas = $this->_getOrganizationGas($debug);
        foreach($organizationGas as $organization) {
            // dd($organization);
            $sitemap .= $this->_getTagUrl($baseUrl . '/home-' . $organization->j_seo);
            $sitemap .= $this->_getTagUrl($baseUrl . '/home-' . $organization->j_seo . '/consegne-' . $organization->j_seo);
        }

        /*
         * PRODUTTORI
        */
        $suppliers = $this->_getSuppliers($debug);
        foreach($suppliers as $supplier) {
            // dd($supplier);
            $sitemap .= $this->_getTagUrl($this->_neo_portalgas_fe_url . '/site/produttore/' . $supplier->slug);
        }

        $sitemap .= "\n" . '</urlset>';
        $sitemap = Xml::build($sitemap);
        $sitemap =  $sitemap->asXML();

        // return $this->response->withType('text/xml')->withStringBody($sitemap);
        if($debug) debug('file '.$this->_file_path . DS . $this->_file_name);
        $file = new File($this->_file_path . DS . $this->_file_name, true, 0644);
        if($debug) debug($file);
        if($debug) debug($sitemap);
        $file->write($sitemap);
        $file->close();

        return $sitemap;
	}

    private function _getTagUrl($url, $options=[]) {

        (isset($options['priority']))? $priority = $options['priority']: $priority = '0.5';
        (isset($options['lastmod']))? $lastmod = $lastmod['priority']: $lastmod = '';
        (isset($options['changefreq']))? $changefreq = $options['changefreq']: $changefreq = 'yearly';

        $results = '';
        $results .= '<url>' . "\n";
        $results .= '<loc>' . $url .'</loc>' . "\n";
        $results .= '<priority>'.$priority.'</priority>' . "\n";
        if(!empty($changefreq))
            $results .= '<changefreq>'.$changefreq.'</changefreq>' . "\n";

        $results .= '</url>' . "\n";

        return $results;
    }

    private function _getOrganizationGas($debug) {

        $organizationsGasTable = TableRegistry::get('OrganizationsGas');

        $where = ['stato' => 'Y'];
        return $organizationsGasTable->gets($user=null, $where, $debug);
    }

    private function _getSuppliers($debug) {

        $suppliersTable = TableRegistry::get('Suppliers');

        $where = ['Suppliers.stato' => 'Y',
                  'Suppliers.slug != ' => ''];
        return $suppliersTable->find()
                                ->where($where)
                                ->order(['Suppliers.name'])
                                ->all();
    }

    private function _getUrls() {

        $results = [];

        $i=0;
        $results[$i] = $this->_portalgas_fe_url;
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/cos-e-un-g-a-s-gruppo-d-acquisto-solidale';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/gmaps-gas';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/contattaci';
        $i++;
        $results[$i] = $this->_neo_portalgas_fe_url . '/site/produttori';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/gestionale-web-gas';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/360-l-app-di-portalgas-per-iphone-android';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/mobile';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/12-portalgas/2-termini-di-utilizzo';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/12-portalgas/143-come-sono-utilizzati-i-cookies-da-parte-di-portalgas';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/353-economia-solidale-in-festa-15-e-16-ottobre-buttigliera-alta';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/342-l-app-di-portalgas';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/307-des-l-unione-fa-la-forza';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/173-fairphone2-il-primo-telefono-etico';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/144-avigliana-mangia-bio-e-festa-dei-g-a-s';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/139-proposte-per-un-agenda-del-cibo';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/129-integrazione-con-google-calendar';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/119-canale-youtube-di-portalgas';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/95-portalgas-mobile';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/66-l-altra-spesa-e-diventata-grande';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/34-i-10-prodotti-di-uso-comune-che-contribuiscono-alla-deforestazione';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/23-10-consigli-per-l-orto-e-il-giardino-in-estate';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/16-mensa-sana-2013-le-mense-scolastiche-italiane-sono-sempre-piu-bio';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/4-km-zero-essere-o-non-essere-locavoro';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/notizie/3-i-gas-gruppi-di-acquisto-solidale';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/images/manuali/presentazione-slide.pdf';
        $i++;
        $results[$i] = $this->_portalgas_fe_url . '/images/manuali/AppPortalgas-mobile.pdf';

        return $results;
    }
}