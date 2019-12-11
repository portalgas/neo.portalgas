<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KSuppliersOrganizationsReferentsFixture
 */
class KSuppliersOrganizationsReferentsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'supplier_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'group_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'join con user_usergroup_map, usergroups', 'precision' => null, 'autoIncrement' => null],
        'type' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'REFERENTE', 'collate' => 'utf8_general_ci', 'comment' => 'lato front-end viene evidenziata la differenza', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'index_supplier_organization_id' => ['type' => 'index', 'columns' => ['supplier_organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['organization_id', 'user_id', 'supplier_organization_id', 'group_id', 'type'], 'length' => []],
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
                'organization_id' => 1,
                'supplier_organization_id' => 1,
                'user_id' => 1,
                'group_id' => 1,
                'type' => 'a15c7c2c-fbce-4b4e-a8dd-6a91d8cb1bbd',
                'created' => '2019-11-19 20:27:25',
                'modified' => '2019-11-19 20:27:25'
            ],
        ];
        parent::init();
    }
}
