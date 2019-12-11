<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteDetailCalendarsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteDetailCalendarsTable Test Case
 */
class QuoteDetailCalendarsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteDetailCalendarsTable
     */
    public $QuoteDetailCalendars;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteDetailCalendars',
        'app.QuoteDetails',
        'app.Collaborators',
        'app.PriceTypes',
        'app.PayTypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteDetailCalendars') ? [] : ['className' => QuoteDetailCalendarsTable::class];
        $this->QuoteDetailCalendars = TableRegistry::getTableLocator()->get('QuoteDetailCalendars', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteDetailCalendars);

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
