<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteDetailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteDetailsTable Test Case
 */
class QuoteDetailsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteDetailsTable
     */
    public $QuoteDetails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteDetails',
        'app.Quotes',
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
        $config = TableRegistry::getTableLocator()->exists('QuoteDetails') ? [] : ['className' => QuoteDetailsTable::class];
        $this->QuoteDetails = TableRegistry::getTableLocator()->get('QuoteDetails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteDetails);

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
