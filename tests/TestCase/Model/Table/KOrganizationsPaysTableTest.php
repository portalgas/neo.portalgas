<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\KOrganizationsPaysTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\KOrganizationsPaysTable Test Case
 */
class KOrganizationsPaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\KOrganizationsPaysTable
     */
    public $KOrganizationsPays;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.KOrganizationsPays',
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
        $config = TableRegistry::getTableLocator()->exists('KOrganizationsPays') ? [] : ['className' => KOrganizationsPaysTable::class];
        $this->KOrganizationsPays = TableRegistry::getTableLocator()->get('KOrganizationsPays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KOrganizationsPays);

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
