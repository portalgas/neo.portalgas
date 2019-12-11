<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteDetailCalendarStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteDetailCalendarStatesTable Test Case
 */
class QuoteDetailCalendarStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteDetailCalendarStatesTable
     */
    public $QuoteDetailCalendarStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteDetailCalendarStates',
        'app.QuoteDetailCalendars'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteDetailCalendarStates') ? [] : ['className' => QuoteDetailCalendarStatesTable::class];
        $this->QuoteDetailCalendarStates = TableRegistry::getTableLocator()->get('QuoteDetailCalendarStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteDetailCalendarStates);

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
