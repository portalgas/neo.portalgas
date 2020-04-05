<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSummaryOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryOrdersTable Test Case
 */
class KSummaryOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSummaryOrdersTable
     */
    public $KSummaryOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryOrders',
        'app.Organizations',
        'app.Users',
        'app.Deliveries',
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
        $config = TableRegistry::getTableLocator()->exists('KSummaryOrders') ? [] : ['className' => KSummaryOrdersTable::class];
        $this->KSummaryOrders = TableRegistry::getTableLocator()->get('KSummaryOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryOrders);

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
