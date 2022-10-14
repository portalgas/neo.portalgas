<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Router;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

use Authorization\AuthorizationServiceInterface;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;

use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;

use Authorization\Policy\OrmResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// https://book.cakephp.org/authorization/2/en/request-authorization-middleware.html
use Authorization\Middleware\RequestAuthorizationMiddleware;
use App\Policy\RequestPolicy;
use Authorization\Policy\ResolverCollection;
use Authorization\Policy\MapResolver;
use Cake\Http\ServerRequest;

use Cake\Http\Middleware\CsrfProtectionMiddleware;

use App\Middleware\ResponseMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         *
         * https://book.cakephp.org/debugkit/3/en/index.html
         */
        if (Configure::read('debug')) {          
            Configure::write('DebugKit.panels', ['DebugKit.Packages' => false]);
            Configure::write('DebugKit.safeTld', ['dev', 'local', 'neo.portalgas.local']);
            Configure::write('DebugKit.forceEnable', true);
            Configure::write('DebugKit.ignoreAuthorization', true); // Cake Authorization plugin is enabled. If you would like to force DebugKit to ignore it
            $this->addPlugin('DebugKit');
        }

        $this->addPlugin('AssetMix', ['bootstrap' => true]);

        $this->addPlugin('CakeDC/Enum');

        $this->addPlugin('Josegonzalez/Upload');
        $this->addPlugin('DataTables');
        
        $this->addPlugin('ADmad/JwtAuth');
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');
        // $this->addPlugin('CakeImpersonate');
        $this->addPlugin('AdminLTE');
       
        $this->addPlugin('CakePdf', ['bootstrap' => true]); 

        // https://github.com/cwbit/cakephp-sluggable
        $this->addPlugin('Sluggable');       
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middlewareQueue)
    {
        $config = Configure::read('Config');
        $portalgas_bo_url_login = $config['Portalgas.bo.url.login'];
        $portalgas_fe_url_login = $config['Portalgas.fe.url.login']; 
                
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(null, Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime')
            ]))

            // Add routing middleware.
            // Routes collection cache enabled by default, to disable route caching
            // pass null as cacheConfig, example: `new RoutingMiddleware($this)`
            // you might want to disable this cache in case your routing is extremely simple
            ->add(new RoutingMiddleware($this, '_cake_routes_'));

            /*
             * Csrf middleware
             *
             * in default.ctp setto per js var csrfToken
             * ajax aggiungere headers: {'X-CSRF-Token': csrfToken},
             * in automatico per ogni form mi aggiunge il campo hidden _csrfToken
             * se no forzo con HtmlCustom->csrfTokenHidden()
             * per disabiliartlo (ex Api\TokensController.php) $csrf->whitelistCallback / $this->getEventManager()->off($this->Csrf);
            */ 
           
            $csrf = new CsrfProtectionMiddleware();
            // Token check will be skipped when callback returns `true`.
            $csrf->whitelistCallback(function (ServerRequestInterface $request) {            
                $prefix = $request->getParam('prefix'); 
                $prefix = strtolower($prefix);
                /* debug($prefix); */
                if (!empty($prefix) && $prefix === 'api') {
                    return true;
                }
            });            
            $middlewareQueue->add($csrf);
            
            /*
             * Authentication middleware
             */            
            $authentication = new AuthenticationMiddleware($this, [
            /* 
             *  Notice: Deprecated (16384): The `unauthenticatedRedirect` configuration key on AuthenticationMiddleware is deprecated.
                Instead set the `{$key}` on your AuthenticationService instance
             
             'unauthenticatedRedirect' => $portalgas_bo_url_login
             'unauthenticatedRedirect' => Router::url($portalgas_bo_url_login)
             */
            ]);
            $middlewareQueue->add($authentication);

            /*
             * Authorization middleware
             */            
             $authorization = new AuthorizationMiddleware($this, [
                            'requireAuthorizationCheck' => true,
                            'unauthorizedHandler' => [
                                'className' => 'Authorization.Redirect',
                               // 'url' => $portalgas_bo_url_login,
                                'url' => $portalgas_fe_url_login,
                                'queryParam' => 'redirectUrl',
                                'exceptions' => [
                                    MissingIdentityException::class,
                                    OtherException::class,
                                ]
                            ],
                            //'unauthorizedRedirect' => Router::url($portalgas_fe_url_login),
                            //'unauthorizedRedirect' => $portalgas_bo_url_login,
                            'unauthorizedRedirect' => $portalgas_fe_url_login,
                            // https://book.cakephp.org/authorization/2/en/middleware.html#identity-decorator
                             'identityDecorator' => function (AuthorizationServiceInterface $authorization, \ArrayAccess $identity) {
                                    /*
                                     * $identity dev'essere di tipo object(App\Model\Entity\User)
                                     */
                                    return $identity->setAuthorization($authorization);
                                }
                        ]);

            $middlewareQueue->add($authorization);

            $middlewareQueue->add(new RequestAuthorizationMiddleware());

			$middlewareQueue->add(new ResponseMiddleware());
			
        return $middlewareQueue;
    }

    public function getAuthenticationService(ServerRequestInterface $request, ResponseInterface $response)
    {
        $config = Configure::read('Config');
        $portalgas_bo_url_login = $config['Portalgas.bo.url.login'];
        $portalgas_fe_url_login = $config['Portalgas.fe.url.login'];
                
        $service = new AuthenticationService();

        $service->setConfig([
            // 'unauthenticatedRedirect' => Router::url($portalgas_bo_url_login),
            'unauthenticatedRedirect' => Router::url($portalgas_fe_url_login),
            'queryParam' => null
        ]);
        
        $service->loadAuthenticator('Authentication.Session');

        $service->loadAuthenticator('App\Auth\Joomla25Authenticate');

        /*
         * https://book.cakephp.org/authentication/2/en/url-checkers.html
         * loginUrl viene confrontato con Authentication\UrlChecker\UrlCheckerTrait con l'url corrente 
         */
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                'username' => 'username',
                'password' => 'password'
            ]
        ]);

        /* 
         * https://book.cakephp.org/authentication/2/en/password-hashers.html
         * compatibilita' con joomla 2.5
         */
        $service->loadIdentifier(
            'Authentication.Password', [
                'passwordHasher' => [
                    'className' => 'Authentication.Fallback',
                    'hashers' => [
                        'Authentication.Default',
                        [
                            'className' => 'Joomla25'
                        ],
                    ]
                ],              
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'finder' => 'loginActive'  // resolve Users::findLoginActive
                ],       
            ]
        );

        return $service;
    }

    public function getAuthorizationService(ServerRequestInterface $request, ResponseInterface $response)
    {      
        $ormResolver = new OrmResolver();

        $mapResolver = new MapResolver();
        $mapResolver->map(ServerRequest::class, RequestPolicy::class);

        $resolvers = new ResolverCollection([$ormResolver, $mapResolver]);

        return new AuthorizationService($resolvers);
    }

    protected function bootstrapCli()
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');
    }    
}