<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KStatOrdersFixture
 */
class KStatOrdersFixture extends TestFixture
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
        'supplier_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'supplier_organization_name' => ['type' => 'string', 'length' => 225, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'supplier_img1' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'stat_delivery_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'stat_delivery_year' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data_inizio' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'data_fine' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'importo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'tesoriere_fattura_importo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => '0.00', 'comment' => ''],
        'tesoriere_doc1' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'tesoriere_data_pay' => ['type' => 'date', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'tesoriere_importo_pay' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => true, 'default' => '0.00', 'comment' => ''],
        'request_payment_num' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_stat_delivery_id' => ['type' => 'index', 'columns' => ['stat_delivery_id'], 'length' => []],
            'index_supplier_organization_id' => ['type' => 'index', 'columns' => ['supplier_organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
            'collation' => 'utf8_general_ci'
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
                'supplier_organization_id' => 1,
                'supplier_organization_name' => 'Lorem ipsum dolor sit amet',
                'supplier_img1' => 'Lorem ipsum dolor sit amet',
                'stat_delivery_id' => 1,
                'stat_delivery_year' => 1,
                'data_inizio' => '2020-04-30',
                'data_fine' => '2020-04-30',
                'importo' => 1,
                'tesoriere_fattura_importo' => 1,
                'tesoriere_doc1' => 'Lorem ipsum dolor sit amet',
                'tesoriere_data_pay' => '2020-04-30',
                'tesoriere_importo_pay' => 1,
                'request_payment_num' => 'Lorem ip',
            ],
        ];
        parent::init();
    }
}
