<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JUsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JUsersTable Test Case
 */
class JUsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JUsersTable
     */
    public $JUsers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.JUsers',
        'app.Organizations',
        'app.Suppliers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('JUsers') ? [] : ['className' => JUsersTable::class];
        $this->JUsers = TableRegistry::getTableLocator()->get('JUsers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JUsers);

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
