<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReportsFixture
 */
class ReportsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'descri' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null],
        'subject' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'filter_state' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'filter_data_field' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'filter_data_cond' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_bin', 'comment' => '', 'precision' => null, 'fixed' => null],
        'filter_data_value' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'se 0 => today', 'precision' => null, 'autoIncrement' => null],
        'pay_type_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'is_active' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_bin'
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
                'name' => 'Lorem ipsum dolor sit amet',
                'descri' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'subject' => 'Lorem ipsum dolor sit amet',
                'filter_state' => 1,
                'filter_data_field' => 'Lorem ipsum dolor sit amet',
                'filter_data_cond' => 'Lorem ipsum dolor sit amet',
                'filter_data_value' => 1,
                'pay_type_id' => 1,
                'is_active' => 1,
                'created' => '2019-06-10 09:52:43',
                'modified' => '2019-06-10 09:52:43'
            ],
        ];
        parent::init();
    }
}
