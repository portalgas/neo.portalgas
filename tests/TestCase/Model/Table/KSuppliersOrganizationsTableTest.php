<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSuppliersOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSuppliersOrganizationsTable Test Case
 */
class KSuppliersOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSuppliersOrganizationsTable
     */
    public $KSuppliersOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSuppliersOrganizations',
        'app.Organizations',
        'app.Suppliers',
        'app.CategorySuppliers',
        'app.OwnerOrganizations',
        'app.OwnerSupplierOrganizations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSuppliersOrganizations') ? [] : ['className' => KSuppliersOrganizationsTable::class];
        $this->KSuppliersOrganizations = TableRegistry::getTableLocator()->get('KSuppliersOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSuppliersOrganizations);

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
