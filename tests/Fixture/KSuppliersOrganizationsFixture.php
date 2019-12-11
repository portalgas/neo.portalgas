<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KSuppliersOrganizationsFixture
 */
class KSuppliersOrganizationsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'supplier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 225, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'ripeto il valore di table.suppliers cosi\' quando prendo l\'elenco non devo fare la join', 'precision' => null, 'fixed' => null],
        'category_supplier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => 'ripeto il valore di table.suppliers cosi\' quando prendo l\'elenco non devo fare la join', 'precision' => null, 'autoIncrement' => null],
        'frequenza' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'owner_articles' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'REFERENT', 'collate' => 'latin1_swedish_ci', 'comment' => 'indica se il listino degli articoli associati puo modificarlo il produttor, il referente o il titolare DES', 'precision' => null, 'fixed' => null],
        'owner_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'owner_supplier_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'can_view_orders' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'can_view_orders_users' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'latin1_swedish_ci', 'comment' => 'permessi per vedere gli ordini e gli acquisti dei gasisti', 'precision' => null, 'fixed' => null],
        'can_promotions' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'mail_order_open' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'mail_order_close' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'stato' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf16_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'index_supplier_id' => ['type' => 'index', 'columns' => ['supplier_id'], 'length' => []],
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'organization_id' => 1,
                'supplier_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'category_supplier_id' => 1,
                'frequenza' => 'Lorem ipsum dolor sit amet',
                'owner_articles' => 'Lorem ipsum dolor sit amet',
                'owner_organization_id' => 1,
                'owner_supplier_organization_id' => 1,
                'can_view_orders' => 'Lorem ipsum dolor sit amet',
                'can_view_orders_users' => 'Lorem ipsum dolor sit amet',
                'can_promotions' => 'Lorem ipsum dolor sit amet',
                'mail_order_open' => 'Lorem ipsum dolor sit amet',
                'mail_order_close' => 'Lorem ipsum dolor sit amet',
                'stato' => 'Lorem ipsum dolor sit amet',
                'created' => '2019-11-16 21:28:48',
                'modified' => '2019-11-16 21:28:48'
            ],
        ];
        parent::init();
    }
}
