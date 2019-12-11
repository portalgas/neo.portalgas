<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KSuppliersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KSuppliersTable Test Case
 */
class KSuppliersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KSuppliersTable
     */
    public $KSuppliers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KSuppliers',
        'app.CategorySuppliers',
        'app.JContents',
        'app.DeliveryTypes',
        'app.OwnerOrganizations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KSuppliers') ? [] : ['className' => KSuppliersTable::class];
        $this->KSuppliers = TableRegistry::getTableLocator()->get('KSuppliers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KSuppliers);

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
