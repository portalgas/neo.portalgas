<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteStatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteStatesTable Test Case
 */
class QuoteStatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteStatesTable
     */
    public $QuoteStates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteStates',
        'app.Quotes',
        'app.Reports'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteStates') ? [] : ['className' => QuoteStatesTable::class];
        $this->QuoteStates = TableRegistry::getTableLocator()->get('QuoteStates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteStates);

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
