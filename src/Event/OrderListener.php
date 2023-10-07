<?php
declare(strict_types=1);

namespace App\Event;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;

class OrderListener implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            'OrderListener.setStatus' => [
                'callable' => 'setStatus',
                'priority' => 1
            ],
        ];
    }

    public function setStatus($event, $user, $order=null)
    {
        if(empty($user))
            return false;
            
        $config = Configure::read('Config');
        $portalgas_app_root = $config['Portalgas.App.root'];

        $organization_id = $user->organization->id;
        $debug = 0;
        (!empty($order)) ? $order_id = $order->id: $order_id = '';
        $cmd = 'php -f '.$portalgas_app_root.'/components/com_cake/app/Cron/index.php ordersStatoElaborazione %s %s %s';
        $cmd = sprintf($cmd, $organization_id, $debug, $order_id);
        exec($cmd);

        // Log::write('debug', 'OrderListener.setStatus '.$cmd);
        
        // $lifeCycleOrdersTable = TableRegistry::get('LifeCycleOrders');
        // $lifeCycleOrdersTable->stateCodeUpdate($user, $order, 'OPEN');

        $event->setResult(['order' => $order]);
        return ($event);
    }
}