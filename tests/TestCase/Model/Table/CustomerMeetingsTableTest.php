<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CustomerMeetingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CustomerMeetingsTable Test Case
 */
class CustomerMeetingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CustomerMeetingsTable
     */
    public $CustomerMeetings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.CustomerMeetings',
        'app.Customers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CustomerMeetings') ? [] : ['className' => CustomerMeetingsTable::class];
        $this->CustomerMeetings = TableRegistry::getTableLocator()->get('CustomerMeetings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CustomerMeetings);

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
