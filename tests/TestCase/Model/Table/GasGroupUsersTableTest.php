<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GasGroupUsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GasGroupUsersTable Test Case
 */
class GasGroupUsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GasGroupUsersTable
     */
    public $GasGroupUsers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.GasGroupUsers',
        'app.Organizations',
        'app.Users',
        'app.GasGroups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('GasGroupUsers') ? [] : ['className' => GasGroupUsersTable::class];
        $this->GasGroupUsers = TableRegistry::getTableLocator()->get('GasGroupUsers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GasGroupUsers);

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
