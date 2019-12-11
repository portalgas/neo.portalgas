<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * KArticlesOrdersFixture
 */
class KArticlesOrdersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'order_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'article_organization_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'se ordine DES l\'articolo puo\' riferirsi ad un\'altro gas', 'precision' => null, 'autoIncrement' => null],
        'article_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta_cart' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'prezzo' => ['type' => 'float', 'length' => 11, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => '0.00', 'comment' => ''],
        'pezzi_confezione' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta_minima' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta_massima' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'qta_minima_order' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => 'qta_minima rispetto a tutti gli acquisti', 'precision' => null, 'autoIncrement' => null],
        'qta_massima_order' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'arrivati alla qta indicata, l ordine sull articolo sara bloccato', 'precision' => null, 'autoIncrement' => null],
        'qta_multipli' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'alert_to_qta' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'arrivati alla qta indicata il sistema inviera una mail ai referenti', 'precision' => null, 'autoIncrement' => null],
        'send_mail' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => 'se N invia mail al referente, ex QTAMAX', 'precision' => null, 'fixed' => null],
        'flag_bookmarks' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'N', 'collate' => 'utf8_general_ci', 'comment' => 'se Y e\' gia\' stato processato se e\' tra preferiti degli utenti', 'precision' => null, 'fixed' => null],
        'stato' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'Y', 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'index_organization_id' => ['type' => 'index', 'columns' => ['organization_id'], 'length' => []],
            'index_article_id' => ['type' => 'index', 'columns' => ['article_id'], 'length' => []],
            'index_order_id' => ['type' => 'index', 'columns' => ['order_id'], 'length' => []],
            'index_article_organization_id' => ['type' => 'index', 'columns' => ['article_organization_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['organization_id', 'article_organization_id', 'article_id', 'order_id'], 'length' => []],
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
                'order_id' => 1,
                'article_organization_id' => 1,
                'article_id' => 1,
                'qta_cart' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'prezzo' => 1,
                'pezzi_confezione' => 1,
                'qta_minima' => 1,
                'qta_massima' => 1,
                'qta_minima_order' => 1,
                'qta_massima_order' => 1,
                'qta_multipli' => 1,
                'alert_to_qta' => 1,
                'send_mail' => 'Lorem ipsum dolor sit amet',
                'flag_bookmarks' => 'Lorem ipsum dolor sit amet',
                'stato' => 'Lorem ipsum dolor sit amet',
                'created' => '2019-11-16 21:29:34',
                'modified' => '2019-11-16 21:29:34'
            ],
        ];
        parent::init();
    }
}
