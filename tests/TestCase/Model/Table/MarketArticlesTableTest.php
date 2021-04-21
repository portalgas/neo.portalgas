<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MarketArticlesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MarketArticlesTable Test Case
 */
class MarketArticlesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MarketArticlesTable
     */
    public $MarketArticles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MarketArticles',
        'app.Organizations',
        'app.Markets',
        'app.Articles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MarketArticles') ? [] : ['className' => MarketArticlesTable::class];
        $this->MarketArticles = TableRegistry::getTableLocator()->get('MarketArticles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MarketArticles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
