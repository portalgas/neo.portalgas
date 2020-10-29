<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use Cake\Core\Configure;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    // Register scoped middleware for in scopes.
    /*
    https://book.cakephp.org/3.0/en/controllers/middleware.html#cross-site-request-forgery-csrf-middleware gia' abilitato in AppController $this->loadComponent('Csrf')

    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true
    ]));

    /**
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
    $routes->applyMiddleware('csrf');
    */

    /*
     * generics pages SLUG => pageWidgets
    $builder->connect('/:slug', ['controller' => 'Pages', 'action' => 'display'],
        [
            'pass' => ['slug'],
            'slug' => '[^\/]+'  // '([\w\/.])*'
        ]
    );
     */

    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $config = Configure::read('Config');
    $vue_is_active = $config['Vue.isActive']; 
    if(!$vue_is_active) {
        $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home-backoffice']);        
    }
    else {
        $routes->connect('/', ['controller' => 'Pages', 'action' => 'vue', 'vue']);
        $routes->connect('/*', ['controller' => 'Pages', 'action' => 'vue', 'vue']);
        $routes->connect('/*/*', ['controller' => 'Pages', 'action' => 'vue', 'vue']); 

        /*
         * mapping route gestiti da cakephp e non da vue
         */ 
        $routes->connect('/users/login', ['controller' => 'Users', 'action' => 'login']);  
        $routes->connect('/users/logout', ['controller' => 'Users', 'action' => 'logout']);
    }
    
    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
	
    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *
     * ```
     * $routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
     * $routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
     * ```
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->connect('/vue/index', ['controller' => 'Vue', 'action' => 'index']);

    $routes->fallbacks(DashedRoute::class);
});

/**
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * Router::scope('/api', function (RouteBuilder $routes) {
 *     // No $routes->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */


/*
 * in App\Controller\AppController::isAuthorized gestisco i prefix / Application.php CsrfProtectionMiddleware
 */ 
Router::prefix('api', function (RouteBuilder $routes) { 
    $routes->setExtensions(['json', 'xml']);      

    /*
     * token da portalgas cakephp 2.x
     */ 
    $routes->scope('/joomla25Salt', ['controller' => 'joomla25Salts'], function (RouteBuilder $routes) {
        $routes->connect('/login', ['action' => 'login', '_method' => 'GET']);
    }); 

    /*
    * token jwt, login da front-end
    */ 
    $routes->scope('/tokenJwt', ['controller' => 'TokenJwts'], function (RouteBuilder $routes) {
        $routes->connect('/login', ['action' => 'login', '_method' => 'POST']);
        $routes->connect('/getUser', ['action' => 'getUser', '_method' => 'POST']);
    });
});

Router::prefix('admin', function (RouteBuilder $routes) { 
    $routes->connect('/', ['controller' => 'Dashboards', 'action' => 'index']);
    $routes->fallbacks(DashedRoute::class); 

    $routes->prefix('api', function(RouteBuilder $routes) {
        
        $routes->setExtensions(['json', 'xml']);
        
        /* 
         * ping per mantenere la session
         */
        $routes->scope('/pings', ['controller' => 'Pings'], function (RouteBuilder $routes) {
                $routes->connect('/index', ['action' => 'index', '_method' => 'GET']);     
        });

        /* 
         * queue
         */
        $routes->scope('/queue', ['controller' => 'Queues'], function (RouteBuilder $routes) {
                $routes->connect('/queue', ['action' => 'queue', '_method' => 'POST']);
        });
        /* 
         * richiama queue in loops (ex remote-file)
         */        
        $routes->scope('/queues', ['controller' => 'Queues'], function (RouteBuilder $routes) {
            $routes->connect('/queues', ['action' => 'queues', '_method' => 'POST']);
        }); 

        $routes->scope('/users', ['controller' => 'Users'], function (RouteBuilder $routes) {
            $routes->connect('/getByDelivery', ['action' => 'getByDelivery', '_method' => 'POST']);
        });       
        $routes->scope('/carts', ['controller' => 'Carts'], function (RouteBuilder $routes) {
            $routes->connect('/getUsersByDelivery', ['action' => 'getUsersByDelivery', '_method' => 'POST']);
            $routes->connect('/getUsersCashByDelivery', ['action' => 'getUsersCashByDelivery', '_method' => 'POST']);
        });
        $routes->scope('/cashes', ['controller' => 'Cashes'], function (RouteBuilder $routes) {
            $routes->connect('/excludedUpdate', ['action' => 'cashExcludedUpdate', '_method' => 'POST']);
        });
        $routes->scope('/cashiers', ['controller' => 'Cashiers'], function (RouteBuilder $routes) {
            $routes->connect('/getCompleteUsersByDelivery', ['action' => 'getCompleteUsersByDelivery', '_method' => 'POST']);
        });
        $routes->scope('/OrganizationsPays', ['controller' => 'OrganizationsPays'], function (RouteBuilder $routes) {
            $routes->connect('/setMsgText', ['action' => 'setMsgText', '_method' => 'POST']);
        });
        
        /*
         * servizi ajax
         */ 
        $routes->scope('/', ['controller' => 'Ajaxs'], function (RouteBuilder $routes) {
            $routes->connect('/fieldUpdate', ['action' => 'fieldUpdate', '_method' => 'POST']);
            $routes->connect('/eventUpdate', ['action' => 'eventUpdate', '_method' => 'POST']);
            $routes->connect('/getList', ['action' => 'getList', '_method' => 'POST']);
        }); 

        $routes->scope('/PriceTypes', ['controller' => 'PriceTypes'], function (RouteBuilder $routes) {
            $routes->connect('/getsByOrderId', ['action' => 'getsByOrderId', '_method' => 'POST']);
        });        

        $routes->scope('/SuppliersOrganizations', ['controller' => 'SuppliersOrganizations'], function (RouteBuilder $routes) {
            $routes->connect('/getsById', ['action' => 'getsById', '_method' => 'POST']);
            $routes->connect('/getByOrderId', ['action' => 'getByOrderId', '_method' => 'POST']);
        });        

        /*
         * ecommerce vue
         */
        $routes->scope('/deliveries', ['controller' => 'Deliveries'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
        }); 
        $routes->scope('/orders', ['controller' => 'Orders'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
            $routes->connect('/getArticlesOrdersByOrderId', ['action' => 'getArticlesOrdersByOrderId', '_method' => 'POST']); 
        });    
        $routes->scope('/carts', ['controller' => 'Carts'], function (RouteBuilder $routes) {
            $routes->connect('/managementCart', ['action' => 'managementCart', '_method' => 'POST']);
            $routes->connect('/getByOrder', ['action' => 'getByOrder', '_method' => 'POST']);
        }); 
        $routes->scope('/html-article-orders', ['controller' => 'HtmlArticleOrders'], function (RouteBuilder $routes) {
            $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
        });

        $routes->fallbacks(DashedRoute::class);        
    }); 
});