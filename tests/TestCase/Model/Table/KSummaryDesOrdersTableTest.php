<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSummaryDesOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSummaryDesOrdersTable Test Case
 */
class KSummaryDesOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSummaryDesOrdersTable
     */
    public $KSummaryDesOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSummaryDesOrders',
        'app.Des',
        'app.DesOrders',
        'app.Organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSummaryDesOrders') ? [] : ['className' => KSummaryDesOrdersTable::class];
        $this->KSummaryDesOrders = TableRegistry::getTableLocator()->get('KSummaryDesOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSummaryDesOrders);

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
