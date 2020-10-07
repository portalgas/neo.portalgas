<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KProdGasPromotionsOrganizationsDeliveriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KProdGasPromotionsOrganizationsDeliveriesTable Test Case
 */
class KProdGasPromotionsOrganizationsDeliveriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KProdGasPromotionsOrganizationsDeliveriesTable
     */
    public $KProdGasPromotionsOrganizationsDeliveries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KProdGasPromotionsOrganizationsDeliveries',
        'app.Suppliers',
        'app.ProdGasPromotions',
        'app.Organizations',
        'app.Deliveries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KProdGasPromotionsOrganizationsDeliveries') ? [] : ['className' => KProdGasPromotionsOrganizationsDeliveriesTable::class];
        $this->KProdGasPromotionsOrganizationsDeliveries = TableRegistry::getTableLocator()->get('KProdGasPromotionsOrganizationsDeliveries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KProdGasPromotionsOrganizationsDeliveries);

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
