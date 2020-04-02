<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KOrdersTable Test Case
 */
class KOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KOrdersTable
     */
    public $KOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KOrders',
        'app.Organizations',
        'app.SupplierOrganizations',
        'app.OwnerOrganizations',
        'app.OwnerSupplierOrganizations',
        'app.Deliveries',
        'app.ProdGasPromotions',
        'app.DesOrders',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KOrders') ? [] : ['className' => KOrdersTable::class];
        $this->KOrders = TableRegistry::getTableLocator()->get('KOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KOrders);

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
