<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * JUsergroupsFixture
 */
class JUsergroupsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Primary Key', 'autoIncrement' => true, 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => '0', 'comment' => 'Adjacency List Reference Id', 'precision' => null, 'autoIncrement' => null],
        'lft' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => 'Nested set lft.', 'precision' => null, 'autoIncrement' => null],
        'rgt' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => 'Nested set rgt.', 'precision' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => '', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'idx_usergroup_title_lookup' => ['type' => 'index', 'columns' => ['title'], 'length' => []],
            'idx_usergroup_adjacency_lookup' => ['type' => 'index', 'columns' => ['parent_id'], 'length' => []],
            'idx_usergroup_nested_set_lookup' => ['type' => 'index', 'columns' => ['lft', 'rgt'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'idx_usergroup_parent_title_lookup' => ['type' => 'unique', 'columns' => ['parent_id', 'title'], 'length' => []],
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
                'parent_id' => 1,
                'lft' => 1,
                'rgt' => 1,
                'title' => 'Lorem ipsum dolor sit amet'
            ],
        ];
        parent::init();
    }
}
