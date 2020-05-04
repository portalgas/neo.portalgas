<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KBackupOrdersOrdersFixture
 */
class KBackupOrdersOrdersFixture extends TestFixture
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
        'owner_articles' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'REFERENT', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'owner_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'owner_supplier_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'delivery_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'prod_gas_promotion_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'des_order_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data_inizio' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'data_fine' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'data_fine_validation' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => '1970-01-01', 'comment' => '', 'precision' => null],
        'data_incoming_order' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => '1970-01-01', 'comment' => '', 'precision' => null],
        'data_state_code_close' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => '1970-01-01', 'comment' => 'data quando passa a close', 'precision' => null],
        'nota' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'hasTrasport' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'trasport_type' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'trasport' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'hasCostMore' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cost_more_type' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cost_more' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'hasCostLess' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cost_less_type' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cost_less' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'typeGest' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'state_code' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'mail_open_send' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'mail_open_data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '1970-01-01 00:00:00', 'comment' => 'data di invio della mail per l\'apertura ordine', 'precision' => null],
        'mail_close_data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => '1970-01-01 00:00:00', 'comment' => 'data di invio della mail per la chiusura ordine', 'precision' => null],
        'mail_open_testo' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'type_draw' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'SIMPLE', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'tot_importo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'qta_massima' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta_massima_um' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'send_mail_qta_massima' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'importo_massimo' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'send_mail_importo_massimo' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'tesoriere_nota' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'tesoriere_fattura_importo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'tesoriere_doc1' => ['type' => 'string', 'length' => 256, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'tesoriere_data_pay' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => '1970-01-01', 'comment' => '', 'precision' => null],
        'tesoriere_importo_pay' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'tesoriere_stato_pay' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'inviato_al_tesoriere_da' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'REFERENTE', 'collate' => 'utf8_general_ci', 'comment' => 'chi ha inviato l\'ordine al tesoriere', 'precision' => null, 'fixed' => null],
        'isVisibleFrontEnd' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'isVisibleBackOffice' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_supplier_organization_id' => ['type' => 'index', 'columns' => ['supplier_organization_id'], 'length' => []],
            'index_delivery_id' => ['type' => 'index', 'columns' => ['delivery_id'], 'length' => []],
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
                'owner_articles' => 'Lorem ipsum dolor sit amet',
                'owner_organization_id' => 1,
                'owner_supplier_organization_id' => 1,
                'delivery_id' => 1,
                'prod_gas_promotion_id' => 1,
                'des_order_id' => 1,
                'data_inizio' => '2020-05-02',
                'data_fine' => '2020-05-02',
                'data_fine_validation' => '2020-05-02',
                'data_incoming_order' => '2020-05-02',
                'data_state_code_close' => '2020-05-02',
                'nota' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'hasTrasport' => 'Lorem ipsum dolor sit amet',
                'trasport_type' => 'Lorem ipsum dolor sit amet',
                'trasport' => 1,
                'hasCostMore' => 'Lorem ipsum dolor sit amet',
                'cost_more_type' => 'Lorem ipsum dolor sit amet',
                'cost_more' => 1,
                'hasCostLess' => 'Lorem ipsum dolor sit amet',
                'cost_less_type' => 'Lorem ipsum dolor sit amet',
                'cost_less' => 1,
                'typeGest' => 'Lorem ipsum dolor sit amet',
                'state_code' => 'Lorem ipsum dolor sit amet',
                'mail_open_send' => 'Lorem ipsum dolor sit amet',
                'mail_open_data' => '2020-05-02 20:00:01',
                'mail_close_data' => '2020-05-02 20:00:01',
                'mail_open_testo' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'type_draw' => 'Lorem ipsum dolor sit amet',
                'tot_importo' => 1,
                'qta_massima' => 1,
                'qta_massima_um' => 'Lorem ipsum dolor sit amet',
                'send_mail_qta_massima' => 'Lorem ipsum dolor sit amet',
                'importo_massimo' => 1,
                'send_mail_importo_massimo' => 'Lorem ipsum dolor sit amet',
                'tesoriere_nota' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'tesoriere_fattura_importo' => 1,
                'tesoriere_doc1' => 'Lorem ipsum dolor sit amet',
                'tesoriere_data_pay' => '2020-05-02',
                'tesoriere_importo_pay' => 1,
                'tesoriere_stato_pay' => 'Lorem ipsum dolor sit amet',
                'inviato_al_tesoriere_da' => 'Lorem ipsum dolor sit amet',
                'isVisibleFrontEnd' => 'Lorem ipsum dolor sit amet',
                'isVisibleBackOffice' => 'Lorem ipsum dolor sit amet',
                'created' => '2020-05-02 20:00:01',
                'modified' => '2020-05-02 20:00:01',
            ],
        ];
        parent::init();
    }
}
