<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSuppliersOrganizationsReferentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSuppliersOrganizationsReferentsTable Test Case
 */
class KSuppliersOrganizationsReferentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSuppliersOrganizationsReferentsTable
     */
    public $KSuppliersOrganizationsReferents;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSuppliersOrganizationsReferents',
        'app.Organizations',
        'app.SupplierOrganizations',
        'app.Users',
        'app.Groups'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSuppliersOrganizationsReferents') ? [] : ['className' => KSuppliersOrganizationsReferentsTable::class];
        $this->KSuppliersOrganizationsReferents = TableRegistry::getTableLocator()->get('KSuppliersOrganizationsReferents', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSuppliersOrganizationsReferents);

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
