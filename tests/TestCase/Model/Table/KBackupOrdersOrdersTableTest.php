<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KBackupOrdersOrdersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KBackupOrdersOrdersTable Test Case
 */
class KBackupOrdersOrdersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KBackupOrdersOrdersTable
     */
    public $KBackupOrdersOrders;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBackupOrdersOrders',
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
        $config = TableRegistry::getTableLocator()->exists('KBackupOrdersOrders') ? [] : ['className' => KBackupOrdersOrdersTable::class];
        $this->KBackupOrdersOrders = TableRegistry::getTableLocator()->get('KBackupOrdersOrders', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KBackupOrdersOrders);

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
