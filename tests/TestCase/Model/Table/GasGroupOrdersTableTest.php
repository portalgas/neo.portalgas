<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GasGroupOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GasGroupOrdersTable Test Case
 */
class GasGroupOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GasGroupOrdersTable
     */
    public $GasGroupOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.GasGroupOrders',
        'app.Organizations',
        'app.GasGroups',
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
        $config = TableRegistry::getTableLocator()->exists('GasGroupOrders') ? [] : ['className' => GasGroupOrdersTable::class];
        $this->GasGroupOrders = TableRegistry::getTableLocator()->get('GasGroupOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GasGroupOrders);

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
