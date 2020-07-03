<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OrdersTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OrdersTypesTable Test Case
 */
class OrdersTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OrdersTypesTable
     */
    public $OrdersTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.OrdersTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('OrdersTypes') ? [] : ['className' => OrdersTypesTable::class];
        $this->OrdersTypes = TableRegistry::getTableLocator()->get('OrdersTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->OrdersTypes);

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
}
