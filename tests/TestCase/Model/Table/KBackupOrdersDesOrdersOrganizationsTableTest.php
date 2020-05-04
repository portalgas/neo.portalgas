<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KBackupOrdersDesOrdersOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KBackupOrdersDesOrdersOrganizationsTable Test Case
 */
class KBackupOrdersDesOrdersOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KBackupOrdersDesOrdersOrganizationsTable
     */
    public $KBackupOrdersDesOrdersOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KBackupOrdersDesOrdersOrganizations',
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
        $config = TableRegistry::getTableLocator()->exists('KBackupOrdersDesOrdersOrganizations') ? [] : ['className' => KBackupOrdersDesOrdersOrganizationsTable::class];
        $this->KBackupOrdersDesOrdersOrganizations = TableRegistry::getTableLocator()->get('KBackupOrdersDesOrdersOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KBackupOrdersDesOrdersOrganizations);

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
