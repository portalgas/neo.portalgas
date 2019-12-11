<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EventTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EventTypesTable Test Case
 */
class EventTypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\EventTypesTable
     */
    public $EventTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.EventTypes',
        'app.Events'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('EventTypes') ? [] : ['className' => EventTypesTable::class];
        $this->EventTypes = TableRegistry::getTableLocator()->get('EventTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EventTypes);

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
}
