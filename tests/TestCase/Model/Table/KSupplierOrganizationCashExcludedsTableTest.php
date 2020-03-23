<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSupplierOrganizationCashExcludedsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSupplierOrganizationCashExcludedsTable Test Case
 */
class KSupplierOrganizationCashExcludedsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSupplierOrganizationCashExcludedsTable
     */
    public $KSupplierOrganizationCashExcludeds;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSupplierOrganizationCashExcludeds',
        'app.Organizations',
        'app.SupplierOrganizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSupplierOrganizationCashExcludeds') ? [] : ['className' => KSupplierOrganizationCashExcludedsTable::class];
        $this->KSupplierOrganizationCashExcludeds = TableRegistry::getTableLocator()->get('KSupplierOrganizationCashExcludeds', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSupplierOrganizationCashExcludeds);

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
