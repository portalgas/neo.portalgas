<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KStatCartsFixture
 */
class KStatCartsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'stat_order_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'importo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'index_article_organization_id' => ['type' => 'index', 'columns' => ['article_organization_id'], 'length' => []],
            'index_article_id' => ['type' => 'index', 'columns' => ['article_id'], 'length' => []],
            'index_stat_order_id' => ['type' => 'index', 'columns' => ['stat_order_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['organization_id', 'user_id', 'article_id', 'stat_order_id'], 'length' => []],
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
                'organization_id' => 1,
                'user_id' => 1,
                'article_organization_id' => 1,
                'article_id' => 1,
                'stat_order_id' => 1,
                'qta' => 1,
                'importo' => 1,
            ],
        ];
        parent::init();
    }
}
