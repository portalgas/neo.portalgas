<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\QuoteVoicesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\QuoteVoicesTable Test Case
 */
class QuoteVoicesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\QuoteVoicesTable
     */
    public $QuoteVoices;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.QuoteVoices',
        'app.Offers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('QuoteVoices') ? [] : ['className' => QuoteVoicesTable::class];
        $this->QuoteVoices = TableRegistry::getTableLocator()->get('QuoteVoices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->QuoteVoices);

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
