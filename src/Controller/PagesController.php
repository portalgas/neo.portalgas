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
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Filesystem\File;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['display', 'vueGuest']);
        $this->Authorization->skipAuthorization(['display', 'vueGuest']);

        $this->loadComponent('ProdGasPromotion');
    }

    /* 
     * vue login
     *
     * $routes->connect('/', ['controller' => 'Pages', 'action' => 'vue', 'vue']); ...
     *
     * view src\Template\Pages\vue.ctp => $this->layout = 'vue';   
     */
    public function vue() {
        // $user = $this->Authentication->getIdentity();
        // debug($user);

        $hasGasUsersPromotions = false;
        $hasSocialMarketOrders = false;

        $user = $this->Authentication->getIdentity();

        if(!empty($user) && !empty($user->organization)) {
            $organization_id = $user->organization->id;

            $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');
            $hasGasUsersPromotions = $prodGasPromotionsOrganizationsTable->hasGasUsersPromotions($organization_id);

            if(Configure::read('social_market_organization_id')!=false) {

                $ordersTable = TableRegistry::get('Orders');
                $ordersTable = $ordersTable->factory($user, Configure::read('social_market_organization_id'), Configure::read('Order.type.socialmarket'));

                $where = [];
                $where['orders'] = ['state_code IN ' => ['OPEN']];
                $ordersSocialMarkets = $ordersTable->gets($user, Configure::read('social_market_organization_id'), $where);
                ($ordersSocialMarkets->count()==0) ? $hasSocialMarketOrders = false: $hasSocialMarketOrders = true;
            }
        }

        $this->set(compact('hasGasUsersPromotions', 'hasSocialMarketOrders'));
    }

    /* 
     * site - vue without login
     *
     * $routes->connect('/site', ['controller' => 'Pages', 'action' => 'vueGuest', 'vueGuest']);  ...
     *
     * /site/produttori
     *
     * view src\Template\Pages\vue_guest.ctp => $this->layout = 'vue';
     */
    public function vueGuest() {

        $hasGasUsersPromotions = false;

        $user = $this->Authentication->getIdentity();

        if(!empty($user) && isset($user->organization)) {
            $organization_id = $user->organization->id;

            $prodGasPromotionsOrganizationsTable = TableRegistry::get('ProdGasPromotionsOrganizations');
            $hasGasUsersPromotions = $prodGasPromotionsOrganizationsTable->hasGasUsersPromotions($organization_id);
        }

        $this->set(compact('hasGasUsersPromotions')); 
    }

    /* 
     * vue without login
    public function socialMarket() {
        $hasGasUsersPromotions = false;
        $this->set(compact('hasGasUsersPromotions'));
    }
     */


    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {  
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }  
}