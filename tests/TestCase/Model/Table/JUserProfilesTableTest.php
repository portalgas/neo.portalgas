<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\JUserProfilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\JUserProfilesTable Test Case
 */
class JUserProfilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JUserProfilesTable
     */
    public $JUserProfiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.JUserProfiles',
        'app.Users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('JUserProfiles') ? [] : ['className' => JUserProfilesTable::class];
        $this->JUserProfiles = TableRegistry::getTableLocator()->get('JUserProfiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JUserProfiles);

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
