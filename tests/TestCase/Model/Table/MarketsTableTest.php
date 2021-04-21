<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MarketsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MarketsTable Test Case
 */
class MarketsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MarketsTable
     */
    public $Markets;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Markets',
        'app.Organizations',
        'app.MarketArticles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Markets') ? [] : ['className' => MarketsTable::class];
        $this->Markets = TableRegistry::getTableLocator()->get('Markets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Markets);

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
