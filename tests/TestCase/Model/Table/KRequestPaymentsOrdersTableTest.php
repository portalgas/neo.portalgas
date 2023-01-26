<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KRequestPaymentsOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KRequestPaymentsOrdersTable Test Case
 */
class KRequestPaymentsOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KRequestPaymentsOrdersTable
     */
    public $KRequestPaymentsOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KRequestPaymentsOrders',
        'app.Organizations',
        'app.Deliveries',
        'app.Orders',
        'app.RequestPayments',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KRequestPaymentsOrders') ? [] : ['className' => KRequestPaymentsOrdersTable::class];
        $this->KRequestPaymentsOrders = TableRegistry::getTableLocator()->get('KRequestPaymentsOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KRequestPaymentsOrders);

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
