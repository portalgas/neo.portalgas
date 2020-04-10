<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSummaryOrderAggregatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryOrderAggregatesTable Test Case
 */
class KSummaryOrderAggregatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSummaryOrderAggregatesTable
     */
    public $KSummaryOrderAggregates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryOrderAggregates',
        'app.Organizations',
        'app.Users',
        'app.Orders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSummaryOrderAggregates') ? [] : ['className' => KSummaryOrderAggregatesTable::class];
        $this->KSummaryOrderAggregates = TableRegistry::getTableLocator()->get('KSummaryOrderAggregates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryOrderAggregates);

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
