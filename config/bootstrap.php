<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Inflector;
use Cake\Utility\Security;

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 *
 * It is HIGHLY discouraged to use a .env file in production, due to security risks
 * and decreased performance on each request. The purpose of the .env file is to emulate
 * the presence of the environment variables like they would be present in production.
 */
if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
     $dotenv->parse()
         ->putenv()
         ->toEnv()
         ->toServer();
}

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
	
    $application_env = env('APPLICATION_ENV', 'production');
    Configure::load('database_'.$application_env, 'default');
    // debug(Configure::read('Datasources'));
    Configure::load('config_'.$application_env, 'default'); 
    // debug(Configure::read('Config'));       
	
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
//Configure::load('app_local', 'default');

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Email::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
/*
Type::build('time')->useImmutable();
Type::build('date')->useImmutable();
Type::build('datetime')->useImmutable();
Type::build('timestamp')->useImmutable();
*/
Type::build('time')->useImmutable();
Type::build('date')->useImmutable();
Type::build('datetime')->useImmutable();
Type::build('timestamp')->useImmutable();

\Cake\I18n\Time::setToStringFormat('dd/MM/yyyy HH:mm');
\Cake\I18n\Date::setToStringFormat('dd/MM/yyyy');
\Cake\I18n\FrozenTime::setToStringFormat('dd/MM/yyyy'); //  HH:mm:ss
\Cake\I18n\FrozenDate::setToStringFormat('dd/MM/yyyy');
 
Type::build('decimal')->useLocaleParser();
//Type::build('float')->useLocaleParser(); se no non salva i decimali del float!
Type::build('date')->useLocaleParser();
Type::build('datetime')->useLocaleParser();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

// $this->addPlugin('BootstrapUI');
Configure::write('BootstrapUIEnabled', false);

Configure::load('adminlte', 'default');
Configure::load('auth', 'default');

$this->addPlugin('Bootstrap'); // https://holt59.github.io/cakephp3-bootstrap-helpers/
$this->addPlugin('CakeDC/Enum');

Configure::write('HtmlOptionEmpty', [null => __('-------')]);

Configure::write('AdminLTEMenu', false);  // a true per avere i menu di demo  

// table class="dataTables table table-striped table-hover"
Configure::write('dataTables.active', true);
if(Configure::read('dataTables.active'))
    Configure::write('paginate.limit', 10000); 
else 
    Configure::write('paginate.limit', 20);  // default

Configure::write('ckeditor5.toolbar', "['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable']");

Configure::write('icon_is_system', ['OK' => 'fa fa-lock', 'KO' => 'fa fa-unlock-alt']);

Configure::write('group_id_root',8);
Configure::write('group_id_root_supplier',24);
Configure::write('group_id_manager',10);  
Configure::write('group_id_manager_delivery',20);
Configure::write('group_id_generic',60); // Per raggruppare utenti per uso statistico
Configure::write('group_id_referent',18);
Configure::write('group_id_super_referent',19);
Configure::write('group_id_cassiere',21);  // referente cassa (pagamento degli utenti alla consegna)
Configure::write('group_id_referent_cassiere',41);
Configure::write('group_id_manager_des',36);
Configure::write('group_id_super_referent_des',38);
Configure::write('group_id_referent_des',37);
Configure::write('group_id_titolare_des_supplier',39);
Configure::write('group_id_des_supplier_all_gas', 51);
Configure::write('group_id_user_manager_des', 77);
Configure::write('group_id_user_flag_privacy', 78);
Configure::write('group_id_referent_tesoriere',23);  // referente tesoriere (pagamento con richiesta degli utenti dopo consegna) gestisce anche il pagamento del suo produttore
Configure::write('group_id_tesoriere',11); // tesoriere (pagamento ai fornitori)
Configure::write('group_id_storeroom',9); 
Configure::write('group_id_user',2);  
Configure::write('group_id_events',65); // calendar events gasEvents
Configure::write('group_system',66); // system info@gas.portalgas.it dispensa@gas.portalgas.it

/*
 * altre tipologie di organization GAS PRODGAS PACT
 */
Configure::write('prod_gas_supplier_manager',62); // prodGasSupplier
Configure::write('group_pact_supplier_manager',84);   // manager pact 

Configure::write('DB.field.date.error', '0000-00-00');
Configure::write('DB.field.date.empty', '1970-01-01');
Configure::write('DB.field.double.empty', '0.00');
Configure::write('DB.field.datetime.empty', '1970-01-01 00:00:00');

Configure::write('routes_msg_stop', ['controller' => 'Pages', 'action' => 'msg_stop', 'prefix' => 'admin']);
Configure::write('routes_msg_not_order_state', ['controller' => 'Pages', 'action' => 'msg_not_order_state', 'prefix' => 'admin']);

Configure::write('QueueLogs', false);          // disattivare in production
Configure::write('QueueLogs.database', false); // attivare QueueLogs
Configure::write('QueueLogs.file', false);     // attivare QueueLogs
Configure::write('QueueLogs.shell', false);    // attivare QueueLogs

Configure::write('site.name', 'PortAlGas');

Configure::write('SupplierOrganizationStatoIni', 'Y');
Configure::write('SupplierOrganizationMailOrderOpenIni', 'Y');
Configure::write('SupplierOrganizationMailOrderCloseIni', 'Y');
Configure::write('SupplierOrganizationOwnerArticlesIni', 'REFERENT');  
Configure::write('SupplierOrganizationCanViewOrdersIni', 'Y');
Configure::write('SupplierOrganizationCanViewOrdersUserIni', 'Y');
Configure::write('SupplierOrganizationCanPromotionsIni', 'Y');


Configure::write('Gdxp.protocolVersion', '1.0');
Configure::write('Gdxp.applicationSignature', 'PortAlGas');
Configure::write('Gdxp.file.prefix', 'gdxp-');

// {organizaton_id} / {img1}
Configure::write('Article.img.path.full', 'http://www.portalgas.it/images/articles/%s/%s');
Configure::write('Article.img.preview.width', '50px');

Configure::write('Supplier.img.path.full', 'http://www.portalgas.it/images/organizations/contents/%s');
Configure::write('Supplier.img.path.fulljs', 'http://www.portalgas.it/images/organizations/contents/');
Configure::write('Supplier.img.preview.width', '50px');

Configure::write('Gdxp.suppliers.index.url', 'http://www.economiasolidale.net/api/v1/list.php');
// Configure::write('Gdxp.suppliers.index.url', '/json/gdxp-suppliers.json');
Configure::write('Gdxp.articles.index.url', 'http://www.economiasolidale.net/api/v1/get.php');
Configure::write('Gdxp.queue.code', 'GDXP-PORTALGAS');
Configure::write('Gdxp.schema_path', WWW_ROOT.'/json/json-schema.json');

/*
 * pagamenti
 */
Configure::write('OrganizationPayImportMax', 80); // massimo importo per il canone annuo
Configure::write('costToUser', 1); // quanti euro costa ad utente
Configure::write('OrganizationPayFasceYearStart', 2018); // anno da cui partono le fasce
Configure::write('App.doc.upload.organizations.pays', DS.'images'.DS.'pays');
Configure::write('App.web.doc.upload.organizations.pays', '/images/pays');
Configure::write('OrganizationPayBeneficiarioMarcoLabel', 'Marco Siviero');
Configure::write('OrganizationPayBeneficiarioMarcoIbanLabel', 'Siviero Marco e Sivera Laura');
Configure::write('OrganizationPayBeneficiarioMarcoIban', 'IT79N0301503200000000234427');
Configure::write('OrganizationPayBeneficiarioMarcoCell', '339 402 8600');
Configure::write('OrganizationPayBeneficiarioMarcoMail', 'marco.siviero@portalgas.it'); 

Configure::write('OrganizationPayBeneficiarioFrancescoLabel', 'Francesco Actis Grosso');
Configure::write('OrganizationPayBeneficiarioFrancescoIbanLabel', 'Francesco Actis Grosso e Sara Besacchi');
Configure::write('OrganizationPayBeneficiarioFrancescoIban', 'IT05P0347501605CC0010194166');
Configure::write('OrganizationPayBeneficiarioFrancescoCell', '347 491 5588');
Configure::write('OrganizationPayBeneficiarioFrancescoMail', 'francesco@portalgas.it'); 

Configure::write('separatoreDecimali', ',');
Configure::write('separatoreMigliaia', '.');