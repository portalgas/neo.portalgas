<?php
namespace App\Model\Validation;

use Cake\Core\Configure;
use Cake\Validation\Validation;
use Cake\ORM\TableRegistry;
use App\Traits;

class OrderGasGroupsValidation extends Validation
{
    use Traits\SqlTrait;
    use Traits\UtilTrait;

    /*
     * override di OrderValidation::orderDuplicate
     *
     * ctrl che non esista gia' un'ordine sulla consegna
     * per il gruppo
     */
    public static function orderDuplicate($value, $context)
    {
        // debug($context);
        $organization_id = $context['data']['organization_id'];
        $delivery_id = $context['data']['delivery_id'];
        $order_type_id = $context['data']['order_type_id'];
        $supplier_organization_id = $context['data']['supplier_organization_id'];
        $gas_group_id = $context['data']['gas_group_id'];

        /*
            * se e' PROMOTION posso avere il medesimo ordine su una consegna
            * se e' ordine GasGroup posso avere il medesimo ordine (GasGroupParent) su una consegna
            */
        $type_draws = ['SIMPLE', 'COMPLETE'];

        $ordersTable = TableRegistry::get('Orders');

        $where = ['Orders.organization_id' => $organization_id,
                    'Orders.delivery_id' => $delivery_id,
                    'Orders.supplier_organization_id' => $supplier_organization_id,
                    'Orders.order_type_id' => $order_type_id,
                    'Orders.gas_group_id' => $gas_group_id,
                    'Orders.type_draw IN ' => $type_draws];

        /*
        * workaround => per edit: validator e' definito 'on' => ['create'] non va
        */
        if(!$context['newRecord']) {
            $where += ['Orders.id !=' => $context['data']['id']];
        }

        // debug($where);
        $results = $ordersTable->find()
                            ->where($where)
                            ->first();

        // debug($results);
        if(!empty($results))
            return false;
        else
            return true;
    }

    /*
     * ctrl l'ordine padre abbia articoli associati all'ordine
     */
    public static function totArticles($value, $context)
    {
        // debug($context);
        $organization_id = $context['data']['organization_id'];
        $parent_id = $context['data']['parent_id'];

        $where = ['organization_id' => $organization_id,
                  'order_id' => $parent_id,
                  'stato !=' => 'N'];

        $articlesOrdersTable = TableRegistry::get('ArticlesOrders');
        $totale = $articlesOrdersTable->find()
                        ->where($where)
                        ->count();
        if($totale==0)
            return false;
        else
            return true;
    }

    public static function dateFine($value, $context)
    {
        // debug($context);

        $operator = '<=';
    	$value = $context['data']['data_fine'];
        $value2 = $context['data']['parent_data_fine']; // 15/03/2023

        if(empty($value2))
            return false;

        list($day, $month, $year) = explode('/', $value2);

        $value = $value['year'].$value['month'].$value['day'];
        $value2 = $year.$month.$day;
        if (!Validation::comparison($value, $operator, $value2))
            return false;

        return true;
    }
}
