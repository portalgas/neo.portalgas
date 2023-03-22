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
    https://book.cakephp.org/4/en/controllers/middleware.html#cross-site-request-forgery-csrf-middleware gia' abilitato in Applcation.php

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
        // $routes->connect('/', ['controller' => 'Pages', 'action' => 'vue', 'vue']); accesso alla pagina della home con autenticazione
        $routes->connect('/', ['controller' => 'Pages', 'action' => 'vueGuest', 'vueGuest']);
        $routes->connect('/*', ['controller' => 'Pages', 'action' => 'vue', 'vue']);
        $routes->connect('/*/*', ['controller' => 'Pages', 'action' => 'vue', 'vue']);
        $routes->connect('/site', ['controller' => 'Pages', 'action' => 'vueGuest', 'vueGuest']);  // senza autenitcazione (socialMarket)
        $routes->connect('/site/*', ['controller' => 'Pages', 'action' => 'vueGuest', 'vueGuest']);  // senza autenitcazione (socialMarket)
        $routes->connect('/site/*/*', ['controller' => 'Pages', 'action' => 'vueGuest', 'vueGuest']);  // senza autenitcazione (socialMarket)

        /*
         * mapping route gestiti da cakephp e non da vue
         */ 
        $routes->connect('/users/login', ['controller' => 'Users', 'action' => 'login']);  
        $routes->connect('/users/logout', ['controller' => 'Users', 'action' => 'logout']);
        $routes->connect('/users/logout_bo', ['controller' => 'Users', 'action' => 'logoutBo']);
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

    $routes->connect('/sitemap.xml', ['controller' => 'SiteMaps', 'action' => 'index']);

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
     * token da portalgas cakephp 2.x => neo.portalgas
     */ 
    $routes->scope('/joomla25Salt', ['controller' => 'joomla25Salts'], function (RouteBuilder $routes) {
        $routes->connect('/login', ['action' => 'login', '_method' => 'GET']);
    }); 

    /*
    * token jwt, login da front-end
    */ 
    $routes->scope('/tokenJwt', ['controller' => 'TokenJwts'], function (RouteBuilder $routes) {
        $routes->connect('/login', ['action' => 'login', '_method' => 'POST']);
        $routes->connect('/getByOrder', ['action' => 'getByOrder', '_method' => 'POST']);
    });

    /*
     * vue
     */
    $routes->scope('/social-market', ['controller' => 'Markets'], function (RouteBuilder $routes) {
        $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
        // $routes->connect('/getArticles', ['action' => 'getArticles', '_method' => 'GET']);

        $routes->connect('/getArticles/:market_id', ['action' => 'getArticles', '_method' => 'GET'],
            [
                'pass' => ['market_id'],
                'market_id' => '[0-9]+'
            ]);

    });

    $routes->scope('/categories-suppliers', ['controller' => 'CategoriesSuppliers'], function (RouteBuilder $routes) {
        $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
    });
    $routes->scope('/suppliers', ['controller' => 'Suppliers'], function (RouteBuilder $routes) {
        $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
        $routes->connect('/produttoriGets', ['action' => 'prodGasSupplierGets', '_method' => 'POST']);
        $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
        $routes->connect('/getBySlug', ['action' => 'getBySlug', '_method' => 'POST']);
    });  
    $routes->scope('/regions', ['controller' => 'Regions'], function (RouteBuilder $routes) {
        $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
    });
    $routes->scope('/provinces', ['controller' => 'provinces'], function (RouteBuilder $routes) {
        $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
    });
    $routes->scope('/html-suppliers', ['controller' => 'HtmlSuppliers'], function (RouteBuilder $routes) {
        $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
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
            $routes->connect('/setNota', ['action' => 'setNota', '_method' => 'POST']);
        });
        $routes->scope('/cashes', ['controller' => 'Cashes'], function (RouteBuilder $routes) {
            $routes->connect('/excludedUpdate', ['action' => 'cashExcludedUpdate', '_method' => 'POST']);
            $routes->connect('/cashHistoryByUser', ['action' => 'cashHistoryByUser', '_method' => 'POST']);
        });
        $routes->scope('/cashiers', ['controller' => 'Cashiers'], function (RouteBuilder $routes) {
            $routes->connect('/getCompleteUsersByDelivery', ['action' => 'getCompleteUsersByDelivery', '_method' => 'POST']);
        });
        $routes->scope('/OrganizationsPays', ['controller' => 'OrganizationsPays'], function (RouteBuilder $routes) {
            $routes->connect('/setMsgText', ['action' => 'setMsgText', '_method' => 'POST']);
        });
        
        /*
         * mail
         */
        $routes->scope('/mails', ['controller' => 'Mails'], function (RouteBuilder $routes) {
            $routes->connect('/request-delivery-new', ['action' => 'requestDeliveryNew', '_method' => 'POST']);
        });

        /*
         * gdxp
         */
        $routes->scope('/gdpx', ['controller' => 'GdxpExports'], function (RouteBuilder $routes) {
            $routes->connect('/send-articles', ['action' => 'sendArticles', '_method' => 'POST']);
        });

        /*
         * export
         */
        $routes->scope('/exports', ['controller' => 'Exports'], function (RouteBuilder $routes) {
            $routes->setExtensions(['pdf']);
            $routes->connect('/user-cart', ['action' => 'userCart', '_method' => 'GET']);
            $routes->connect('/user-promotion-cart', ['action' => 'userPromotionCart', '_method' => 'GET']);
        });
        $routes->scope('/exports-referents', ['controller' => 'ExportsReferents'], function (RouteBuilder $routes) {
            $routes->setExtensions(['pdf']);
            $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
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
            // $routes->connect('/import', ['action' => 'import', '_method' => 'POST']);
        });     

        $routes->scope('/ProdGasSuppliers', ['controller' => 'ProdGasSuppliers'], function (RouteBuilder $routes) {
            $routes->connect('/import', ['action' => 'import', '_method' => 'POST']);
        });     

        /*
         * gas groups
         */
        $routes->scope('/gas-group-deliveries', ['controller' => 'GasGroupDeliveries'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
        }); 

        /*
         * ecommerce vue
         */
        $routes->scope('/deliveries', ['controller' => 'Deliveries'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
            $routes->connect('/user-cart-gets', ['action' => 'userCartGets', '_method' => 'POST']);
        }); 
        $routes->scope('/orders', ['controller' => 'Orders'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
            $routes->connect('/user-cart-gets', ['action' => 'userCartGets', '_method' => 'POST']);
            $routes->connect('/getArticlesOrdersByOrderId', ['action' => 'getArticlesOrdersByOrderId', '_method' => 'POST']); 
        });
        $routes->scope('/storerooms', ['controller' => 'Storerooms'], function (RouteBuilder $routes) {
            $routes->connect('/user-cart-gets', ['action' => 'userCartGets', '_method' => 'POST']);
        }); 
        $routes->scope('/carts', ['controller' => 'Carts'], function (RouteBuilder $routes) {
            $routes->connect('/managementCart', ['action' => 'managementCart', '_method' => 'POST']);
            $routes->connect('/getByOrder', ['action' => 'getByOrder', '_method' => 'POST']);
            $routes->connect('/getTotImportByOrderId', ['action' => 'getTotImportByOrderId', '_method' => 'POST']);
        }); 
        $routes->scope('/promotion-carts', ['controller' => 'Carts'], function (RouteBuilder $routes) {
            $routes->connect('/managementCart', ['action' => 'managementCartProdGasPromotionGasUser', '_method' => 'POST']);
        });
        $routes->scope('/social-markets', ['controller' => 'socialMarkets'], function (RouteBuilder $routes) {
            $routes->connect('/user-cart-gets', ['action' => 'userCartGets', '_method' => 'POST']);
        });
        $routes->scope('/users', ['controller' => 'Users'], function (RouteBuilder $routes) {
            $routes->connect('/cash-ctrl-limit', ['action' => 'cashCtrlLimit', '_method' => 'POST']);
        });
        /*
         * non + utilizzata, sostituita da /article-orders
         */
        $routes->scope('/html-article-orders', ['controller' => 'HtmlArticleOrders'], function (RouteBuilder $routes) {
            $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
            $routes->connect('/getCartsByArticles', ['action' => 'getCartsByArticles', '_method' => 'POST']);
        });
        $routes->scope('/article-orders', ['controller' => 'ArticleOrders'], function (RouteBuilder $routes) {
            $routes->connect('/get', ['action' => 'get', '_method' => 'POST']);
            $routes->connect('/getAssociateToOrder', ['action' => 'getAssociateToOrder', '_method' => 'POST']);
            $routes->connect('/setAssociateToOrder', ['action' => 'setAssociateToOrder', '_method' => 'POST']);
            $routes->connect('/setAssociateToPreviousOrder', ['action' => 'setAssociateToPreviousOrder', '_method' => 'POST']);
        });
        $routes->scope('/promotions', ['controller' => 'ProdGasPromotions'], function (RouteBuilder $routes) {
            $routes->connect('/gets', ['action' => 'gets', '_method' => 'POST']);
            $routes->connect('/user-cart-gets', ['action' => 'userCartGets', '_method' => 'POST']);
        });

        $routes->scope('/html-menus', ['controller' => 'HtmlMenus'], function (RouteBuilder $routes) {
            $routes->connect('/order', ['action' => 'orders', '_method' => 'GET']);
        });

        $routes->fallbacks(DashedRoute::class);        
    }); 
});