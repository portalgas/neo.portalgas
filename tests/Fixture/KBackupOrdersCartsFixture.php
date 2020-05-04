<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KBackupOrdersCartsFixture
 */
class KBackupOrdersCartsFixture extends TestFixture
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
        'order_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'deleteToReferent' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'qta_forzato' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'importo_forzato' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => true, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'nota' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'inStoreroom' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'stato' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'date' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'index_order_id' => ['type' => 'index', 'columns' => ['order_id'], 'length' => []],
            'index_article_id' => ['type' => 'index', 'columns' => ['article_id'], 'length' => []],
            'index_article_organization_id' => ['type' => 'index', 'columns' => ['article_organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['organization_id', 'user_id', 'order_id', 'article_organization_id', 'article_id'], 'length' => []],
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
                'order_id' => 1,
                'article_organization_id' => 1,
                'article_id' => 1,
                'qta' => 1,
                'deleteToReferent' => 'Lorem ipsum dolor sit amet',
                'qta_forzato' => 1,
                'importo_forzato' => 1,
                'nota' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'inStoreroom' => 'Lorem ipsum dolor sit amet',
                'stato' => 'Lorem ipsum dolor sit amet',
                'created' => '2020-05-02 20:01:16',
                'date' => 1588449676,
            ],
        ];
        parent::init();
    }
}
