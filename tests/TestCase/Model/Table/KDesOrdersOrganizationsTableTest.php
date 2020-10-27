<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KDesOrdersOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KDesOrdersOrganizationsTable Test Case
 */
class KDesOrdersOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KDesOrdersOrganizationsTable
     */
    public $KDesOrdersOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KDesOrdersOrganizations',
        'app.Des',
        'app.DesOrders',
        'app.Organizations',
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
        $config = TableRegistry::getTableLocator()->exists('KDesOrdersOrganizations') ? [] : ['className' => KDesOrdersOrganizationsTable::class];
        $this->KDesOrdersOrganizations = TableRegistry::getTableLocator()->get('KDesOrdersOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KDesOrdersOrganizations);

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
