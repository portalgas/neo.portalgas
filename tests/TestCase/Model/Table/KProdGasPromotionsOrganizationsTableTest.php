<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KProdGasPromotionsOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KProdGasPromotionsOrganizationsTable Test Case
 */
class KProdGasPromotionsOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KProdGasPromotionsOrganizationsTable
     */
    public $KProdGasPromotionsOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KProdGasPromotionsOrganizations',
        'app.ProdGasPromotions',
        'app.Organizations',
        'app.Orders',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KProdGasPromotionsOrganizations') ? [] : ['className' => KProdGasPromotionsOrganizationsTable::class];
        $this->KProdGasPromotionsOrganizations = TableRegistry::getTableLocator()->get('KProdGasPromotionsOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KProdGasPromotionsOrganizations);

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
