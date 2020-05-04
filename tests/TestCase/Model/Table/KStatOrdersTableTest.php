<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KStatOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KStatOrdersTable Test Case
 */
class KStatOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KStatOrdersTable
     */
    public $KStatOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KStatOrders',
        'app.Organizations',
        'app.SupplierOrganizations',
        'app.StatDeliveries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KStatOrders') ? [] : ['className' => KStatOrdersTable::class];
        $this->KStatOrders = TableRegistry::getTableLocator()->get('KStatOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KStatOrders);

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
