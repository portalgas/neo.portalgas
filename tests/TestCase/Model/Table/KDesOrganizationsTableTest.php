<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KDesOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KDesOrganizationsTable Test Case
 */
class KDesOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KDesOrganizationsTable
     */
    public $KDesOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KDesOrganizations',
        'app.Des',
        'app.Organizations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KDesOrganizations') ? [] : ['className' => KDesOrganizationsTable::class];
        $this->KDesOrganizations = TableRegistry::getTableLocator()->get('KDesOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KDesOrganizations);

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
