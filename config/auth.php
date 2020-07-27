<?php 
/*
 * $this->Auth->_config['token']['authorizationHeader']
 */

return [
    'Auth' => [
            'loginAction' => [
                'prefix' => 'admin/api', // x conflitto con route::api / route::export
                'controller' => 'Tokens',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Dashboards',
                'action' => 'index',
                'home'
            ],
            'flash' => [
                'element' => 'default',
                'key' => 'auth',
                'params' => ['class' => ['alert', 'alert-danger', 'alert-dismissible']]
            ],      
            'authError' => __('Area riservata, inserire le proprie credenziali'),
            'storage' => 'Session',
            'unauthorizedRedirect' => false,
               // 'checkAuthIn' => 'Controller.initialize'          
        ]
];