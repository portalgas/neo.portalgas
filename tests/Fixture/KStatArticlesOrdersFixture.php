<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KStatArticlesOrdersFixture
 */
class KStatArticlesOrdersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'stat_order_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore', 'precision' => null, 'fixed' => null],
        'codice' => ['type' => 'string', 'length' => 25, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore', 'precision' => null, 'fixed' => null],
        'prezzo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore'],
        'qta' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore'],
        'um' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore', 'precision' => null, 'fixed' => null],
        'um_riferimento' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'lo prendo da articles così se cambia l articolo ho il suo valore', 'precision' => null, 'fixed' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_stat_order_id' => ['type' => 'index', 'columns' => ['stat_order_id'], 'length' => []],
            'index_article_organization_id' => ['type' => 'index', 'columns' => ['article_organization_id'], 'length' => []],
            'index_article_id' => ['type' => 'index', 'columns' => ['article_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['organization_id', 'stat_order_id', 'article_id', 'article_organization_id'], 'length' => []],
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
                'stat_order_id' => 1,
                'article_organization_id' => 1,
                'article_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'codice' => 'Lorem ipsum dolor sit a',
                'prezzo' => 1,
                'qta' => 1,
                'um' => 'Lorem ipsum dolor sit amet',
                'um_riferimento' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
