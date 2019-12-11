<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KOrganizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KOrganizationsTable Test Case
 */
class KOrganizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KOrganizationsTable
     */
    public $KOrganizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KOrganizations',
        'app.Templates',
        'app.JPageCategories',
        'app.Gcalendars'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('KOrganizations') ? [] : ['className' => KOrganizationsTable::class];
        $this->KOrganizations = TableRegistry::getTableLocator()->get('KOrganizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KOrganizations);

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
